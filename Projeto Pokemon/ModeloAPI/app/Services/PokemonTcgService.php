<?php

namespace App\Services;

use App\Support\PokemonTypes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class PokemonTcgService
{
    private string $baseUrl = 'https://api.pokemontcg.io/v2';

    public function searchCards(array $filters): array
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $pageSize = max(1, min(48, (int) ($filters['pageSize'] ?? 12)));
        $query = $this->buildQuery($filters);
        $params = [
            'page' => $page,
            'pageSize' => $pageSize,
            'orderBy' => $this->orderBy($filters),
            'select' => 'id,name,number,rarity,types,images,set.id,set.name,set.series,set.releaseDate,hp,attacks,weaknesses,resistances,retreatCost,subtypes,supertype,evolvesFrom,tcgplayer.prices,cardmarket.prices',
        ];

        if ($query !== '') {
            $params['q'] = $query;
        }

        $cacheKey = 'tcg:cards:' . md5(json_encode($params));

        return $this->remember($cacheKey, now()->addMinutes(20), function () use ($params, $filters, $page, $pageSize) {
            $response = $this->request('cards', $params);

            if (!$response || !$response->successful()) {
                return $this->fallbackCardSearch($filters, $page, $pageSize);
            }

            $data = $response->json();

            return is_array($data)
                ? $data
                : $this->fallbackCardSearch($filters, $page, $pageSize);
        });
    }

    public function findCard(string $id): ?array
    {
        return $this->remember('tcg:card:' . $id, now()->addDay(), function () use ($id) {
            $response = $this->request("cards/{$id}");

            if (!$response || !$response->successful()) {
                return $this->fallbackCardById($id);
            }

            return $response->json('data') ?: $this->fallbackCardById($id);
        });
    }

    public function randomCardId(): ?string
    {
        $page = random_int(1, 80);
        $data = $this->searchCards([
            'page' => $page,
            'pageSize' => 1,
        ]);

        if (!empty($data['data'][0]['id'])) {
            return $data['data'][0]['id'];
        }

        $fallback = $this->fallbackCards();

        return $fallback[array_rand($fallback)]['id'] ?? null;
    }

    public function battleDeck(array $pokemonTypes, int $size = 20): array
    {
        $tcgTypes = $this->toTcgTypes($pokemonTypes);
        $pool = [];

        foreach (array_slice($tcgTypes, 0, 1) as $type) {
            $data = $this->remember('tcg:battle-pool:' . $type, now()->addHours(6), function () use ($type) {
                $response = $this->request('cards', [
                    'q' => $this->pokemonSupertypeQuery() . ' types:' . $type,
                    'page' => 1,
                    'pageSize' => 60,
                    'orderBy' => '-set.releaseDate',
                    'select' => 'id,name,hp,types,attacks,weaknesses,retreatCost,subtypes,images,set.name,number',
                ]);

                if (!$response || !$response->successful()) {
                    return [];
                }

                return $response->json('data') ?? [];
            });

            $pool = array_merge($pool, $data);
        }

        $pool = array_values(array_filter($pool, fn ($card) => !empty($card['attacks']) && !empty($card['hp'])));

        if (count($pool) < 8) {
            $pool = array_merge($pool, $this->fallbackBattleCards($tcgTypes[0] ?? 'Colorless'));
        }

        shuffle($pool);
        $deck = [];
        $nameCount = [];

        foreach ($pool as $card) {
            $name = $card['name'] ?? Str::uuid()->toString();

            if (($nameCount[$name] ?? 0) >= 2) {
                continue;
            }

            $deck[] = $this->normalizeBattleCard($card);
            $nameCount[$name] = ($nameCount[$name] ?? 0) + 1;

            if (count($deck) >= $size) {
                break;
            }
        }

        while (count($deck) < $size) {
            foreach ($this->fallbackBattleCards($tcgTypes[0] ?? 'Colorless') as $card) {
                $deck[] = $this->normalizeBattleCard($card);

                if (count($deck) >= $size) {
                    break 2;
                }
            }
        }

        shuffle($deck);

        return array_slice($deck, 0, $size);
    }

    private function request(string $path, array $query = [])
    {
        $pending = Http::connectTimeout(3)->timeout(6);
        $apiKey = config('services.pokemon_tcg.key');

        if ($apiKey) {
            $pending = $pending->withHeaders(['X-Api-Key' => $apiKey]);
        }

        try {
            return $pending->get("{$this->baseUrl}/{$path}", $query);
        } catch (Throwable) {
            return null;
        }
    }

    private function remember(string $key, mixed $ttl, callable $callback): mixed
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (Throwable) {
            return $callback();
        }
    }

    private function buildQuery(array $filters): string
    {
        $parts = [];

        if (!empty($filters['search'])) {
            $search = preg_replace('/[^a-zA-Z0-9 .\'-]/', '', (string) $filters['search']);
            $search = trim((string) $search);

            if ($search !== '') {
                $parts[] = str_contains($search, ' ')
                    ? 'name:"' . addcslashes($search, '"') . '"'
                    : 'name:' . $search . '*';
            }
        }

        if (!empty($filters['pokemonOnly'])) {
            $parts[] = $this->pokemonSupertypeQuery();
        }

        if (!empty($filters['rarity'])) {
            $rarity = preg_replace('/[^a-zA-Z0-9 .\'-]/', '', (string) $filters['rarity']);
            $parts[] = 'rarity:"' . addcslashes($rarity, '"') . '"';
        }

        if (!empty($filters['type'])) {
            $type = preg_replace('/[^a-zA-Z]/', '', (string) $filters['type']);
            $parts[] = 'types:' . $type;
        }

        if (!empty($filters['stage'])) {
            $stage = preg_replace('/[^a-zA-Z0-9 ]/', '', (string) $filters['stage']);

            if ($stage !== '') {
                $parts[] = 'subtypes:"' . addcslashes($stage, '"') . '"';
            }
        }

        if (!empty($filters['setId'])) {
            $setId = preg_replace('/[^a-zA-Z0-9-]/', '', (string) $filters['setId']);
            $parts[] = 'set.id:' . $setId;
        }

        if (!empty($filters['set'])) {
            $set = preg_replace('/[^a-zA-Z0-9 .\'-]/', '', (string) $filters['set']);
            $set = trim((string) $set);

            if ($set !== '') {
                $parts[] = 'set.name:"' . addcslashes($set, '"') . '"';
            }
        }

        return implode(' ', $parts);
    }

    private function pokemonSupertypeQuery(): string
    {
        return 'supertype:Pok' . "\u{00E9}" . 'mon';
    }

    private function orderBy(array $filters): string
    {
        return match ($filters['sort'] ?? '') {
            'name-asc' => 'name',
            'name-desc' => '-name',
            default => '-set.releaseDate',
        };
    }

    private function toTcgTypes(array $pokemonTypes): array
    {
        $reverse = [
            'normal' => 'Colorless',
            'fire' => 'Fire',
            'water' => 'Water',
            'electric' => 'Lightning',
            'grass' => 'Grass',
            'ice' => 'Water',
            'fighting' => 'Fighting',
            'poison' => 'Psychic',
            'ground' => 'Fighting',
            'flying' => 'Colorless',
            'psychic' => 'Psychic',
            'bug' => 'Grass',
            'rock' => 'Fighting',
            'ghost' => 'Psychic',
            'dragon' => 'Dragon',
            'dark' => 'Darkness',
            'steel' => 'Metal',
            'fairy' => 'Fairy',
        ];

        $types = [];

        foreach (PokemonTypes::normalizeMany($pokemonTypes) as $type) {
            $types[] = $reverse[$type] ?? 'Colorless';
        }

        return array_values(array_unique($types));
    }

    private function normalizeBattleCard(array $card): array
    {
        $types = $card['types'] ?? ['Colorless'];
        $attacks = [];

        foreach (($card['attacks'] ?? []) as $attack) {
            $attacks[] = [
                'name' => $attack['name'] ?? 'Ataque',
                'cost' => $attack['cost'] ?? [],
                'damage' => $attack['damage'] ?? '10',
                'text' => $attack['text'] ?? '',
                'convertedEnergyCost' => (int) ($attack['convertedEnergyCost'] ?? count($attack['cost'] ?? [])),
            ];
        }

        return [
            'instanceId' => (string) Str::uuid(),
            'id' => $card['id'] ?? (string) Str::uuid(),
            'name' => $card['name'] ?? 'Pokemon',
            'hp' => (int) preg_replace('/\D+/', '', (string) ($card['hp'] ?? 60)),
            'types' => $types,
            'pokemonTypes' => array_map(fn ($type) => PokemonTypes::tcgMap()[$type] ?? 'normal', $types),
            'attacks' => $attacks ?: [[
                'name' => 'Investida',
                'cost' => ['Colorless'],
                'damage' => '10',
                'text' => '',
                'convertedEnergyCost' => 1,
            ]],
            'weaknesses' => $card['weaknesses'] ?? [],
            'retreatCost' => $card['retreatCost'] ?? [],
            'subtypes' => $card['subtypes'] ?? [],
            'image' => $card['images']['large'] ?? $card['images']['small'] ?? null,
            'set' => $card['set']['name'] ?? 'TCG',
            'number' => $card['number'] ?? null,
            'attached' => [],
            'damageTaken' => 0,
        ];
    }

    private function fallbackBattleCards(string $type): array
    {
        $energy = $type === 'Colorless' ? 'Colorless' : $type;

        return [
            [
                'id' => 'fallback-1',
                'name' => 'Pikachu de Treino',
                'hp' => '70',
                'types' => [$type],
                'attacks' => [
                    ['name' => 'Choque Rapido', 'cost' => [$energy], 'damage' => '20', 'text' => 'Um golpe simples para comecar.'],
                    ['name' => 'Ataque Veloz', 'cost' => [$energy, 'Colorless'], 'damage' => '40', 'text' => ''],
                ],
                'images' => ['large' => $this->fallbackCardImage('Pikachu de Treino', [$type], '70', 'Treino', 'Basic')],
            ],
            [
                'id' => 'fallback-2',
                'name' => 'Eevee de Treino',
                'hp' => '80',
                'types' => ['Colorless'],
                'attacks' => [
                    ['name' => 'Investida', 'cost' => ['Colorless'], 'damage' => '20', 'text' => ''],
                    ['name' => 'Avanco', 'cost' => [$energy, 'Colorless'], 'damage' => '50', 'text' => ''],
                ],
                'images' => ['large' => $this->fallbackCardImage('Eevee de Treino', ['Colorless'], '80', 'Treino', 'Basic')],
            ],
            [
                'id' => 'fallback-3',
                'name' => 'Snorlax de Treino',
                'hp' => '120',
                'types' => ['Colorless'],
                'attacks' => [
                    ['name' => 'Peso Pesado', 'cost' => ['Colorless', 'Colorless'], 'damage' => '40', 'text' => ''],
                    ['name' => 'Impacto', 'cost' => [$energy, 'Colorless', 'Colorless'], 'damage' => '70', 'text' => ''],
                ],
                'images' => ['large' => $this->fallbackCardImage('Snorlax de Treino', ['Colorless'], '120', 'Treino', 'Basic')],
            ],
        ];
    }

    private function fallbackCardSearch(array $filters, int $page, int $pageSize): array
    {
        $cards = $this->filterFallbackCards($this->fallbackCards(), $filters);
        $cards = $this->sortFallbackCards($cards, (string) ($filters['sort'] ?? ''));
        $totalCount = count($cards);
        $offset = max(0, ($page - 1) * $pageSize);

        return [
            'data' => array_slice($cards, $offset, $pageSize),
            'page' => $page,
            'pageSize' => $pageSize,
            'count' => min($pageSize, max(0, $totalCount - $offset)),
            'totalCount' => $totalCount,
        ];
    }

    private function filterFallbackCards(array $cards, array $filters): array
    {
        $search = strtolower(trim((string) ($filters['search'] ?? '')));
        $type = strtolower(trim((string) ($filters['type'] ?? '')));
        $rarity = strtolower(trim((string) ($filters['rarity'] ?? '')));
        $set = strtolower(trim((string) ($filters['set'] ?? '')));
        $setId = strtolower(trim((string) ($filters['setId'] ?? '')));
        $stage = strtolower(trim((string) ($filters['stage'] ?? '')));
        $pokemonOnly = !empty($filters['pokemonOnly']);

        return array_values(array_filter($cards, function (array $card) use ($search, $type, $rarity, $set, $setId, $stage, $pokemonOnly) {
            if ($pokemonOnly && !$this->isPokemonCard($card)) {
                return false;
            }

            if ($search !== '' && !str_contains(strtolower((string) ($card['name'] ?? '')), $search)) {
                return false;
            }

            if ($type !== '') {
                $cardTypes = array_map(fn ($value) => strtolower((string) $value), $card['types'] ?? []);
                if (!in_array($type, $cardTypes, true)) {
                    return false;
                }
            }

            if ($rarity !== '' && strtolower((string) ($card['rarity'] ?? '')) !== $rarity) {
                return false;
            }

            if ($set !== '' && !str_contains(strtolower((string) ($card['set']['name'] ?? '')), $set)) {
                return false;
            }

            if ($setId !== '' && strtolower((string) ($card['set']['id'] ?? '')) !== $setId) {
                return false;
            }

            if ($stage !== '') {
                $subtypes = array_map(fn ($value) => strtolower((string) $value), $card['subtypes'] ?? []);
                if (!in_array($stage, $subtypes, true)) {
                    return false;
                }
            }

            return true;
        }));
    }

    private function sortFallbackCards(array $cards, string $sort): array
    {
        usort($cards, function (array $a, array $b) use ($sort) {
            return match ($sort) {
                'name-asc' => strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? '')),
                'name-desc' => strcasecmp((string) ($b['name'] ?? ''), (string) ($a['name'] ?? '')),
                default => strcmp((string) ($b['set']['releaseDate'] ?? ''), (string) ($a['set']['releaseDate'] ?? '')),
            };
        });

        return $cards;
    }

    private function fallbackCardById(string $id): ?array
    {
        foreach ($this->fallbackCards() as $card) {
            if (($card['id'] ?? '') === $id) {
                return $card;
            }
        }

        return null;
    }

    private function isPokemonCard(array $card): bool
    {
        $supertype = strtolower(str_replace(["\u{00E9}", 'Ã©'], 'e', (string) ($card['supertype'] ?? '')));

        return $supertype === 'pokemon';
    }

    private function fallbackCards(): array
    {
        $cards = [
            ['base1-4', 'Charizard', 'Rare Holo', ['Fire'], 'Stage 2', 'Charmeleon', '120', 'Base', 'Base', '1999/01/09', '4', 399.99],
            ['base1-2', 'Blastoise', 'Rare Holo', ['Water'], 'Stage 2', 'Wartortle', '100', 'Base', 'Base', '1999/01/09', '2', 149.99],
            ['base1-15', 'Venusaur', 'Rare Holo', ['Grass'], 'Stage 2', 'Ivysaur', '100', 'Base', 'Base', '1999/01/09', '15', 119.99],
            ['base1-58', 'Pikachu', 'Common', ['Lightning'], 'Basic', null, '40', 'Base', 'Base', '1999/01/09', '58', 4.99],
            ['base1-46', 'Charmander', 'Common', ['Fire'], 'Basic', null, '50', 'Base', 'Base', '1999/01/09', '46', 3.49],
            ['base1-44', 'Bulbasaur', 'Common', ['Grass'], 'Basic', null, '40', 'Base', 'Base', '1999/01/09', '44', 3.49],
            ['base1-63', 'Squirtle', 'Common', ['Water'], 'Basic', null, '40', 'Base', 'Base', '1999/01/09', '63', 3.49],
            ['swsh4-25', 'Charizard', 'Rare', ['Fire'], 'Stage 2', 'Charmeleon', '170', 'Vivid Voltage', 'Sword & Shield', '2020/11/13', '25', 2.99],
            ['swsh4-43', 'Wailord', 'Rare Holo', ['Water'], 'Stage 1', 'Wailmer', '200', 'Vivid Voltage', 'Sword & Shield', '2020/11/13', '43', 0.75],
            ['swsh4-60', 'Pikachu VMAX', 'Rare Ultra', ['Lightning'], 'VMAX', 'Pikachu V', '310', 'Vivid Voltage', 'Sword & Shield', '2020/11/13', '60', 8.50],
            ['swsh4-4', 'Orbeetle VMAX', 'Rare Holo VMAX', ['Grass'], 'VMAX', 'Orbeetle V', '310', 'Vivid Voltage', 'Sword & Shield', '2020/11/13', '4', 1.20],
            ['swsh4-70', 'Mewtwo', 'Rare', ['Psychic'], 'Basic', null, '120', 'Vivid Voltage', 'Sword & Shield', '2020/11/13', '70', 1.10],
        ];

        return array_map(fn (array $card) => $this->fallbackCard(...$card), $cards);
    }

    private function fallbackCard(
        string $id,
        string $name,
        string $rarity,
        array $types,
        string $stage,
        ?string $evolvesFrom,
        string $hp,
        string $setName,
        string $series,
        string $releaseDate,
        string $number,
        float $price
    ): array {
        $image = $this->fallbackCardImage($name, $types, $hp, $rarity, $stage);
        $setId = Str::before($id, '-');

        return [
            'id' => $id,
            'name' => $name,
            'supertype' => 'Pok' . "\u{00E9}" . 'mon',
            'subtypes' => [$stage],
            'hp' => $hp,
            'types' => $types,
            'evolvesFrom' => $evolvesFrom,
            'number' => $number,
            'rarity' => $rarity,
            'images' => [
                'small' => $image,
                'large' => $image,
            ],
            'set' => [
                'id' => $setId,
                'name' => $setName,
                'series' => $series,
                'releaseDate' => $releaseDate,
            ],
            'attacks' => [[
                'name' => 'Ataque',
                'cost' => [$types[0] ?? 'Colorless'],
                'damage' => '30',
                'text' => 'Carta carregada pelo fallback local quando a API externa nao esta disponivel.',
                'convertedEnergyCost' => 1,
            ]],
            'weaknesses' => [],
            'resistances' => [],
            'retreatCost' => ['Colorless'],
            'tcgplayer' => [
                'prices' => [
                    'normal' => ['market' => $price],
                    'holofoil' => ['market' => $rarity === 'Rare Holo' ? $price : null],
                ],
            ],
            'cardmarket' => [
                'prices' => [
                    'averageSellPrice' => round($price * 0.92, 2),
                    'trendPrice' => round($price * 0.9, 2),
                ],
            ],
            'is_fallback' => true,
        ];
    }

    private function fallbackCardImage(string $name, array $types, string $hp, string $rarity, string $stage): string
    {
        $primaryType = $types[0] ?? 'Colorless';
        $color = $this->tcgTypeColor($primaryType);
        $accent = $this->lightenColor($color, 0.42);
        $dark = $this->darkenColor($color, 0.5);
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeType = htmlspecialchars($primaryType, ENT_QUOTES, 'UTF-8');
        $safeRarity = htmlspecialchars(strtoupper($rarity), ENT_QUOTES, 'UTF-8');
        $safeStage = htmlspecialchars($stage, ENT_QUOTES, 'UTF-8');
        $safeHp = htmlspecialchars($hp, ENT_QUOTES, 'UTF-8');
        $initial = htmlspecialchars(strtoupper(mb_substr($name, 0, 1)), ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="734" height="1024" viewBox="0 0 734 1024">
  <defs>
    <linearGradient id="card" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#fff2a8"/>
      <stop offset="0.18" stop-color="{$accent}"/>
      <stop offset="1" stop-color="{$dark}"/>
    </linearGradient>
    <radialGradient id="orb" cx="35%" cy="25%" r="70%">
      <stop offset="0" stop-color="#ffffff" stop-opacity="0.95"/>
      <stop offset="0.36" stop-color="{$accent}"/>
      <stop offset="1" stop-color="{$color}"/>
    </radialGradient>
    <filter id="shadow">
      <feDropShadow dx="0" dy="16" stdDeviation="16" flood-color="#030712" flood-opacity="0.45"/>
    </filter>
  </defs>
  <rect width="734" height="1024" rx="44" fill="#f6cf4f"/>
  <rect x="26" y="26" width="682" height="972" rx="34" fill="url(#card)" stroke="#2b1703" stroke-width="10"/>
  <rect x="58" y="76" width="618" height="78" rx="18" fill="#fff7cf" opacity="0.95"/>
  <text x="82" y="126" font-family="Arial, sans-serif" font-size="38" font-weight="900" fill="#172033">{$safeName}</text>
  <text x="596" y="126" font-family="Arial, sans-serif" font-size="26" font-weight="900" text-anchor="end" fill="#a30f2d">HP {$safeHp}</text>
  <rect x="58" y="188" width="618" height="452" rx="26" fill="#f8fbff" stroke="#2b1703" stroke-width="8"/>
  <rect x="76" y="206" width="582" height="416" rx="18" fill="#162033"/>
  <circle cx="367" cy="404" r="152" fill="url(#orb)" filter="url(#shadow)"/>
  <path d="M240 404h254" stroke="#172033" stroke-width="28" stroke-linecap="round"/>
  <circle cx="367" cy="404" r="54" fill="#172033" stroke="#fff7cf" stroke-width="14"/>
  <text x="367" y="426" font-family="Arial, sans-serif" font-size="64" font-weight="900" text-anchor="middle" fill="#fff7cf">{$initial}</text>
  <rect x="92" y="664" width="550" height="54" rx="16" fill="#fff7cf" opacity="0.95"/>
  <text x="112" y="700" font-family="Arial, sans-serif" font-size="25" font-weight="900" fill="#172033">{$safeStage}</text>
  <text x="622" y="700" font-family="Arial, sans-serif" font-size="22" font-weight="900" text-anchor="end" fill="#172033">{$safeRarity}</text>
  <rect x="92" y="748" width="550" height="108" rx="18" fill="#fff7cf" opacity="0.92"/>
  <circle cx="133" cy="802" r="23" fill="{$color}" stroke="#172033" stroke-width="5"/>
  <text x="174" y="810" font-family="Arial, sans-serif" font-size="31" font-weight="900" fill="#172033">{$safeType} Strike</text>
  <text x="602" y="810" font-family="Arial, sans-serif" font-size="30" font-weight="900" text-anchor="end" fill="#172033">30</text>
  <text x="92" y="922" font-family="Arial, sans-serif" font-size="20" fill="#fff7cf">Imagem offline gerada pelo sistema</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private function tcgTypeColor(string $type): string
    {
        return [
            'Grass' => '#7AC74C',
            'Fire' => '#EE8130',
            'Water' => '#6390F0',
            'Lightning' => '#F7D02C',
            'Psychic' => '#F95587',
            'Fighting' => '#C22E28',
            'Darkness' => '#705746',
            'Metal' => '#B7B7CE',
            'Dragon' => '#6F35FC',
            'Colorless' => '#A8A77A',
            'Fairy' => '#D685AD',
        ][$type] ?? '#A8A77A';
    }

    private function lightenColor(string $hex, float $amount): string
    {
        return $this->mixColor($hex, '#ffffff', $amount);
    }

    private function darkenColor(string $hex, float $amount): string
    {
        return $this->mixColor($hex, '#07111f', $amount);
    }

    private function mixColor(string $hex, string $target, float $amount): string
    {
        $from = $this->hexToRgb($hex);
        $to = $this->hexToRgb($target);

        $rgb = array_map(
            fn (int $a, int $b) => (int) round($a + (($b - $a) * $amount)),
            $from,
            $to
        );

        return sprintf('#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return [168, 167, 122];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
