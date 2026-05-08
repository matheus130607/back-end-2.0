<?php

namespace App\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PokeApiService
{
    private string $baseUrl = 'https://pokeapi.co/api/v2';

    public function findPokemon(string|int $nameOrId): ?array
    {
        $key = 'pokeapi:pokemon:' . strtolower((string) $nameOrId);

        return Cache::remember($key, now()->addHours(12), function () use ($nameOrId) {
            $response = Http::timeout(8)
                ->retry(2, 200)
                ->get("{$this->baseUrl}/pokemon/" . strtolower((string) $nameOrId));

            if (!$response->successful()) {
                return null;
            }

            return $response->json();
        });
    }

    public function listPokemon(int $offset = 0, int $limit = 36): array
    {
        $offset = max(0, $offset);
        $limit = max(1, min(60, $limit));
        $listKey = "pokeapi:list:{$offset}:{$limit}";

        $list = Cache::remember($listKey, now()->addDay(), function () use ($offset, $limit) {
            $response = Http::timeout(8)
                ->retry(2, 200)
                ->get("{$this->baseUrl}/pokemon", [
                    'offset' => $offset,
                    'limit' => $limit,
                ]);

            if (!$response->successful()) {
                return ['results' => [], 'count' => 1025];
            }

            return $response->json();
        });

        $results = $list['results'] ?? [];
        $missing = [];
        $pokemonsById = [];

        foreach ($results as $item) {
            $id = $this->extractIdFromUrl($item['url'] ?? '');
            $cacheKey = "pokeapi:pokemon-lite:{$id}";

            if ($id && Cache::has($cacheKey)) {
                $pokemonsById[$id] = Cache::get($cacheKey);
                continue;
            }

            if ($id) {
                $missing[$id] = $item['url'];
            }
        }

        if ($missing) {
            $responses = Http::pool(function (Pool $pool) use ($missing) {
                $requests = [];

                foreach ($missing as $id => $url) {
                    $requests[$id] = $pool->as((string) $id)->get($url);
                }

                return $requests;
            });

            foreach ($missing as $id => $url) {
                $response = $responses[(string) $id] ?? null;

                if ($response && $response->successful()) {
                    $lite = $this->formatLite($response->json());
                    $pokemonsById[$id] = $lite;
                    Cache::put("pokeapi:pokemon-lite:{$id}", $lite, now()->addHours(12));
                }
            }
        }

        $pokemons = [];

        foreach ($results as $item) {
            $id = $this->extractIdFromUrl($item['url'] ?? '');
            $pokemons[] = $pokemonsById[$id] ?? $this->formatLiteFallback($id, $item['name'] ?? 'pokemon');
        }

        return [
            'pokemons' => $pokemons,
            'totalCount' => min((int) ($list['count'] ?? 1025), 1025),
        ];
    }

    public function details(int|string $id): ?array
    {
        $cacheKey = "pokeapi:details:{$id}";

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($id) {
            $pokemon = $this->findPokemon($id);

            if (!$pokemon) {
                return null;
            }

            $species = [];
            if (!empty($pokemon['species']['url'])) {
                $species = Cache::remember('pokeapi:species:' . $pokemon['id'], now()->addDay(), function () use ($pokemon) {
                    $response = Http::timeout(8)
                        ->retry(2, 200)
                        ->get($pokemon['species']['url']);

                    return $response->successful() ? $response->json() : [];
                });
            }

            $evolutionChain = [];
            if (!empty($species['evolution_chain']['url'])) {
                $chainId = $this->extractIdFromUrl($species['evolution_chain']['url']);
                $chain = Cache::remember('pokeapi:evolution:' . $chainId, now()->addDay(), function () use ($species) {
                    $response = Http::timeout(8)
                        ->retry(2, 200)
                        ->get($species['evolution_chain']['url']);

                    return $response->successful() ? $response->json() : [];
                });

                if ($chain) {
                    $evolutionChain = $this->parseEvolutionChain($chain);
                }
            }

            return [
                'id' => $pokemon['id'],
                'name' => ucfirst($pokemon['name']),
                'types' => array_map(fn ($type) => $type['type']['name'], $pokemon['types'] ?? []),
                'height' => ($pokemon['height'] ?? 0) / 10,
                'weight' => ($pokemon['weight'] ?? 0) / 10,
                'abilities' => array_map(function ($ability) {
                    return [
                        'name' => $ability['ability']['name'] ?? 'unknown',
                        'is_hidden' => (bool) ($ability['is_hidden'] ?? false),
                    ];
                }, $pokemon['abilities'] ?? []),
                'stats' => array_map(function ($stat) {
                    return [
                        'name' => $stat['stat']['name'] ?? 'stat',
                        'value' => (int) ($stat['base_stat'] ?? 0),
                    ];
                }, $pokemon['stats'] ?? []),
                'images' => [
                    'official' => $pokemon['sprites']['other']['official-artwork']['front_default'] ?? $pokemon['sprites']['front_default'] ?? null,
                    'official_shiny' => $pokemon['sprites']['other']['official-artwork']['front_shiny'] ?? null,
                    'home' => $pokemon['sprites']['other']['home']['front_default'] ?? null,
                    'home_shiny' => $pokemon['sprites']['other']['home']['front_shiny'] ?? null,
                    'front_default' => $pokemon['sprites']['front_default'] ?? null,
                    'front_shiny' => $pokemon['sprites']['front_shiny'] ?? null,
                ],
                'species' => [
                    'color' => $species['color']['name'] ?? 'unknown',
                    'habitat' => $species['habitat']['name'] ?? 'unknown',
                    'generation' => $species['generation']['name'] ?? 'unknown',
                    'capture_rate' => $species['capture_rate'] ?? 0,
                    'base_happiness' => $species['base_happiness'] ?? 70,
                    'flavor_text' => $this->getFlavorText($species),
                ],
                'evolution_chain' => $evolutionChain,
            ];
        });
    }

    public function addMediaUrls(array $pokemon): array
    {
        $id = $pokemon['id'] ?? null;

        if (!$id) {
            return $pokemon;
        }

        $pokemon['sprites']['official_artwork'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$id}.png";
        $pokemon['sprites']['home'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/{$id}.png";
        $pokemon['sprites']['dream_world'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/dream-world/{$id}.svg";
        $pokemon['sprites']['showdown'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/showdown/{$id}.gif";
        $pokemon['sprites']['official_artwork_shiny'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/shiny/{$id}.png";
        $pokemon['sprites']['home_shiny'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/shiny/{$id}.png";
        $pokemon['sprites']['showdown_shiny'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/showdown/shiny/{$id}.gif";
        $pokemon['sprites']['front_shiny'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/shiny/{$id}.png";
        $pokemon['cries'] = [
            'latest' => "https://raw.githubusercontent.com/PokeAPI/cries/main/cries/pokemon/latest/{$id}.ogg",
            'legacy' => "https://raw.githubusercontent.com/PokeAPI/cries/main/cries/pokemon/legacy/{$id}.ogg",
        ];

        return $pokemon;
    }

    private function formatLite(array $data): array
    {
        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'types' => $data['types'] ?? [],
            'image' => $data['sprites']['other']['official-artwork']['front_default'] ?? $data['sprites']['front_default'] ?? null,
            'sprite' => $data['sprites']['front_default'] ?? null,
            'height' => $data['height'] ?? null,
            'weight' => $data['weight'] ?? null,
        ];
    }

    private function formatLiteFallback(?int $id, string $name): array
    {
        return [
            'id' => $id ?: 0,
            'name' => $name,
            'types' => [['type' => ['name' => 'normal']]],
            'image' => $id ? "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$id}.png" : null,
            'sprite' => $id ? "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png" : null,
            'height' => null,
            'weight' => null,
        ];
    }

    private function parseEvolutionChain(array $chain): array
    {
        $evolutions = [];
        $this->walkEvolutionNode($chain['chain'] ?? null, $evolutions);

        return $evolutions;
    }

    private function walkEvolutionNode(?array $node, array &$evolutions): void
    {
        if (!$node) {
            return;
        }

        $pokemonName = $node['species']['name'] ?? '';
        $pokemonId = $this->extractIdFromUrl($node['species']['url'] ?? '');
        $details = $node['evolution_details'][0] ?? [];

        $evolutionData = array_filter([
            'name' => ucfirst($pokemonName),
            'id' => $pokemonId,
            'min_level' => $details['min_level'] ?? null,
            'trigger' => $details['trigger']['name'] ?? null,
            'item' => $details['item']['name'] ?? null,
        ]);

        if ($evolutionData) {
            $evolutions[] = $evolutionData;
        }

        foreach (($node['evolves_to'] ?? []) as $child) {
            $this->walkEvolutionNode($child, $evolutions);
        }
    }

    private function getFlavorText(array $species): string
    {
        foreach (($species['flavor_text_entries'] ?? []) as $entry) {
            if (($entry['language']['name'] ?? '') === 'en') {
                return trim(preg_replace('/\s+/', ' ', $entry['flavor_text']));
            }
        }

        return 'Descricao nao disponivel.';
    }

    private function extractIdFromUrl(string $url): ?int
    {
        if (preg_match('~/(\d+)/?$~', $url, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
