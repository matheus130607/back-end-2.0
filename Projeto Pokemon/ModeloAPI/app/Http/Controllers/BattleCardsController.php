<?php

namespace App\Http\Controllers;

use App\Services\PokemonTcgService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BattleCardsController extends Controller
{
    private const DECK_SIZE = 16;

    private array $energyTypes = [
        'Grass',
        'Fire',
        'Water',
        'Lightning',
        'Psychic',
        'Fighting',
        'Darkness',
        'Metal',
        'Dragon',
        'Colorless',
        'Fairy',
    ];

    public function __construct(private PokemonTcgService $tcg)
    {
    }

    public function index()
    {
        return view('batalha-cartas.index', [
            'energyTypes' => $this->energyTypes,
        ]);
    }

    public function deckBuilder(Request $request)
    {
        $cards = $this->searchBattleCards($request, 18)['cards'];

        return view('batalha-cartas.deck', [
            'initialCards' => $cards,
            'energyTypes' => $this->energyTypes,
            'typeOptions' => $this->energyTypes,
            'stageOptions' => ['Basic', 'Stage 1', 'Stage 2', 'Stage 3'],
            'rarityOptions' => ['Common', 'Uncommon', 'Rare', 'Rare Holo', 'Rare Ultra', 'Rare Secret', 'Illustration Rare', 'Special Illustration Rare'],
        ]);
    }

    public function play()
    {
        return view('batalha-cartas.play', [
            'energyTypes' => $this->energyTypes,
        ]);
    }

    public function cards(Request $request): JsonResponse
    {
        return response()->json($this->searchBattleCards($request, 24));
    }

    public function autoDeck(): JsonResponse
    {
        $deck = $this->buildAutoDeck();

        return response()->json([
            'deck' => $deck,
            'energyTypes' => $this->inferEnergyTypes($deck),
        ]);
    }

    private function searchBattleCards(Request $request, int $pageSize): array
    {
        $data = $this->tcg->searchCards([
            'pokemonOnly' => true,
            'search' => trim((string) $request->input('search', '')),
            'type' => trim((string) $request->input('type', '')),
            'stage' => trim((string) $request->input('stage', '')),
            'rarity' => trim((string) $request->input('rarity', '')),
            'page' => max(1, (int) $request->input('page', 1)),
            'pageSize' => $pageSize,
            'sort' => trim((string) $request->input('sort', 'name-asc')),
        ]);

        $cards = collect($data['data'] ?? [])
            ->filter(fn (array $card) => ($card['supertype'] ?? '') === 'Pokémon' || ($card['supertype'] ?? '') === 'Pokemon')
            ->map(fn (array $card) => $this->normalizeCard($card))
            ->values()
            ->all();

        $totalCount = (int) ($data['totalCount'] ?? count($cards));

        if (empty($cards)) {
            $cards = $this->filterFallbackCards($this->fallbackDeck(), $request);
            $totalCount = count($cards);
            $cards = array_slice($cards, 0, $pageSize);
        }

        return [
            'cards' => $cards,
            'totalCount' => $totalCount,
            'page' => (int) ($data['page'] ?? 1),
            'pageSize' => $pageSize,
        ];
    }

    private function filterFallbackCards(array $cards, Request $request): array
    {
        $search = strtolower(trim((string) $request->input('search', '')));
        $type = $this->normalizeEnergyType(trim((string) $request->input('type', '')));
        $stage = trim((string) $request->input('stage', ''));
        $rarity = strtolower(trim((string) $request->input('rarity', '')));

        return collect($cards)
            ->filter(function (array $card) use ($search, $type, $stage, $rarity, $request) {
                if ($search !== '' && !str_contains(strtolower($card['name']), $search)) {
                    return false;
                }

                if ($request->filled('type') && !in_array($type, $card['types'] ?? [], true)) {
                    return false;
                }

                if ($stage !== '' && ($card['stage'] ?? '') !== $stage) {
                    return false;
                }

                if ($rarity !== '' && strtolower((string) ($card['rarity'] ?? '')) !== $rarity) {
                    return false;
                }

                return true;
            })
            ->values()
            ->all();
    }

    private function buildAutoDeck(): array
    {
        $basicData = $this->tcg->searchCards([
            'pokemonOnly' => true,
            'stage' => 'Basic',
            'page' => random_int(1, 4),
            'pageSize' => 48,
            'sort' => 'name-asc',
        ]);
        $stageOneData = $this->tcg->searchCards([
            'pokemonOnly' => true,
            'stage' => 'Stage 1',
            'page' => random_int(1, 4),
            'pageSize' => 48,
            'sort' => 'name-asc',
        ]);
        $stageTwoData = $this->tcg->searchCards([
            'pokemonOnly' => true,
            'stage' => 'Stage 2',
            'page' => random_int(1, 3),
            'pageSize' => 48,
            'sort' => 'name-asc',
        ]);

        $basics = $this->normalizePool($basicData['data'] ?? []);
        $stageOnes = $this->normalizePool($stageOneData['data'] ?? []);
        $stageTwos = $this->normalizePool($stageTwoData['data'] ?? []);

        if (count($basics) < 4) {
            return $this->fallbackDeck();
        }

        shuffle($basics);
        shuffle($stageOnes);
        shuffle($stageTwos);

        $deck = [];
        $nameCount = [];

        foreach ($basics as $basic) {
            $this->addDeckCard($deck, $nameCount, $basic);

            $stageOne = $this->firstEvolutionFor($basic['name'], $stageOnes);
            if ($stageOne) {
                $this->addDeckCard($deck, $nameCount, $stageOne);

                $stageTwo = $this->firstEvolutionFor($stageOne['name'], $stageTwos);
                if ($stageTwo && count($deck) < self::DECK_SIZE - 2) {
                    $this->addDeckCard($deck, $nameCount, $stageTwo);
                }
            }

            if (count($deck) >= 12) {
                break;
            }
        }

        foreach (array_merge($basics, $stageOnes, $stageTwos, $this->fallbackDeck()) as $card) {
            if ($this->isPlayableAddition($card, $deck)) {
                $this->addDeckCard($deck, $nameCount, $card);
            }

            if (count($deck) >= self::DECK_SIZE) {
                break;
            }
        }

        if (count($deck) < self::DECK_SIZE || !$this->hasBasic($deck)) {
            return $this->fallbackDeck();
        }

        shuffle($deck);

        return array_slice($deck, 0, self::DECK_SIZE);
    }

    private function normalizePool(array $cards): array
    {
        return collect($cards)
            ->filter(fn (array $card) => ($card['supertype'] ?? '') === 'Pokémon' || ($card['supertype'] ?? '') === 'Pokemon')
            ->map(fn (array $card) => $this->normalizeCard($card))
            ->filter(fn (array $card) => !empty($card['name']))
            ->values()
            ->all();
    }

    private function addDeckCard(array &$deck, array &$nameCount, array $card): void
    {
        if (count($deck) >= self::DECK_SIZE) {
            return;
        }

        $name = strtolower((string) ($card['name'] ?? Str::uuid()->toString()));

        if (($nameCount[$name] ?? 0) >= 2) {
            return;
        }

        $deck[] = $card;
        $nameCount[$name] = ($nameCount[$name] ?? 0) + 1;
    }

    private function firstEvolutionFor(string $baseName, array $evolutions): ?array
    {
        $base = $this->cleanName($baseName);

        foreach ($evolutions as $evolution) {
            if ($this->cleanName((string) ($evolution['evolvesFrom'] ?? '')) === $base) {
                return $evolution;
            }
        }

        return null;
    }

    private function isPlayableAddition(array $card, array $deck): bool
    {
        if (($card['stage'] ?? '') === 'Basic') {
            return true;
        }

        $evolvesFrom = $this->cleanName((string) ($card['evolvesFrom'] ?? ''));

        if ($evolvesFrom === '') {
            return false;
        }

        foreach ($deck as $deckCard) {
            if ($this->cleanName((string) ($deckCard['name'] ?? '')) === $evolvesFrom) {
                return true;
            }
        }

        return false;
    }

    private function hasBasic(array $deck): bool
    {
        foreach ($deck as $card) {
            if (($card['stage'] ?? '') === 'Basic') {
                return true;
            }
        }

        return false;
    }

    private function inferEnergyTypes(array $deck): array
    {
        $types = [];

        foreach ($deck as $card) {
            foreach (($card['types'] ?? []) as $type) {
                $type = $this->normalizeEnergyType((string) $type);

                if (!in_array($type, $types, true)) {
                    $types[] = $type;
                }
            }
        }

        return array_slice($types ?: ['Colorless'], 0, 3);
    }

    private function normalizeCard(array $card): array
    {
        $subtypes = array_values($card['subtypes'] ?? []);
        $types = array_values($card['types'] ?? ['Colorless']);
        $attacks = [];

        foreach (($card['attacks'] ?? []) as $attack) {
            $attacks[] = [
                'name' => $attack['name'] ?? 'Ataque',
                'cost' => array_values($attack['cost'] ?? ['Colorless']),
                'damage' => (string) ($attack['damage'] ?? '10'),
                'text' => (string) ($attack['text'] ?? ''),
                'convertedEnergyCost' => (int) ($attack['convertedEnergyCost'] ?? count($attack['cost'] ?? ['Colorless'])),
            ];
        }

        return [
            'id' => (string) ($card['id'] ?? Str::uuid()),
            'name' => (string) ($card['name'] ?? 'Pokemon'),
            'image' => $card['images']['large'] ?? $card['images']['small'] ?? asset('favicon.png'),
            'hp' => max(30, (int) preg_replace('/\D+/', '', (string) ($card['hp'] ?? 50))),
            'types' => array_map(fn ($type) => $this->normalizeEnergyType((string) $type), $types),
            'supertype' => (string) ($card['supertype'] ?? 'Pokemon'),
            'subtypes' => $subtypes,
            'stage' => $this->stageFromSubtypes($subtypes),
            'evolvesFrom' => $card['evolvesFrom'] ?? null,
            'attacks' => $attacks ?: [[
                'name' => 'Investida',
                'cost' => ['Colorless'],
                'damage' => '10',
                'text' => '',
                'convertedEnergyCost' => 1,
            ]],
            'weaknesses' => $card['weaknesses'] ?? [],
            'resistances' => $card['resistances'] ?? [],
            'retreatCost' => $card['retreatCost'] ?? [],
            'rarity' => (string) ($card['rarity'] ?? 'Sem raridade'),
            'set' => [
                'id' => $card['set']['id'] ?? null,
                'name' => $card['set']['name'] ?? 'Colecao',
                'series' => $card['set']['series'] ?? null,
            ],
        ];
    }

    private function stageFromSubtypes(array $subtypes): string
    {
        if (in_array('Stage 2', $subtypes, true)) {
            return 'Stage 2';
        }

        if (in_array('Stage 1', $subtypes, true)) {
            return 'Stage 1';
        }

        if (in_array('Stage 3', $subtypes, true)) {
            return 'Stage 3';
        }

        return 'Basic';
    }

    private function cleanName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/\s+\(.+\)$/', '', $name);
        $name = preg_replace('/[^a-z0-9 ]/', '', (string) $name);

        return trim((string) $name);
    }

    private function normalizeEnergyType(string $type): string
    {
        $lower = strtolower($type);
        $title = ucfirst($lower);

        return match ($lower) {
            'electric', 'lightning' => 'Lightning',
            'dark', 'darkness' => 'Darkness',
            'steel', 'metal' => 'Metal',
            'normal', 'colorless' => 'Colorless',
            default => in_array($title, $this->energyTypes, true) ? $title : 'Colorless',
        };
    }

    private function fallbackDeck(): array
    {
        $cards = [
            ['fallback-charmander', 'Charmander', 70, ['Fire'], 'Basic', null, 4, [['Chama Curta', ['Fire'], '20'], ['Arranhao', ['Colorless'], '10']]],
            ['fallback-charmeleon', 'Charmeleon', 90, ['Fire'], 'Stage 1', 'Charmander', 5, [['Lanca Chamas', ['Fire', 'Colorless'], '40']]],
            ['fallback-charizard', 'Charizard', 150, ['Fire'], 'Stage 2', 'Charmeleon', 6, [['Explosao Ardente', ['Fire', 'Fire', 'Colorless'], '90']]],
            ['fallback-squirtle', 'Squirtle', 70, ['Water'], 'Basic', null, 7, [['Bolhas', ['Water'], '20']]],
            ['fallback-wartortle', 'Wartortle', 100, ['Water'], 'Stage 1', 'Squirtle', 8, [['Jato Dagua', ['Water', 'Colorless'], '40']]],
            ['fallback-blastoise', 'Blastoise', 160, ['Water'], 'Stage 2', 'Wartortle', 9, [['Canhao Hidro', ['Water', 'Water', 'Colorless'], '90']]],
            ['fallback-bulbasaur', 'Bulbasaur', 70, ['Grass'], 'Basic', null, 1, [['Chicote de Vinha', ['Grass'], '20']]],
            ['fallback-ivysaur', 'Ivysaur', 100, ['Grass'], 'Stage 1', 'Bulbasaur', 2, [['Folhas Cortantes', ['Grass', 'Colorless'], '40']]],
            ['fallback-venusaur', 'Venusaur', 160, ['Grass'], 'Stage 2', 'Ivysaur', 3, [['Flor Solar', ['Grass', 'Grass', 'Colorless'], '90']]],
            ['fallback-pikachu', 'Pikachu', 70, ['Lightning'], 'Basic', null, 25, [['Choque', ['Lightning'], '20']]],
            ['fallback-raichu', 'Raichu', 120, ['Lightning'], 'Stage 1', 'Pikachu', 26, [['Raio Forte', ['Lightning', 'Colorless'], '60']]],
            ['fallback-eevee', 'Eevee', 80, ['Colorless'], 'Basic', null, 133, [['Investida', ['Colorless'], '20']]],
            ['fallback-snorlax', 'Snorlax', 150, ['Colorless'], 'Basic', null, 143, [['Impacto Pesado', ['Colorless', 'Colorless'], '60']]],
            ['fallback-lapras', 'Lapras', 120, ['Water'], 'Basic', null, 131, [['Onda Azul', ['Water', 'Colorless'], '50']]],
            ['fallback-machop', 'Machop', 70, ['Fighting'], 'Basic', null, 66, [['Soco', ['Fighting'], '20']]],
            ['fallback-machoke', 'Machoke', 110, ['Fighting'], 'Stage 1', 'Machop', 67, [['Golpe Forte', ['Fighting', 'Colorless'], '50']]],
        ];

        return array_map(fn (array $card) => $this->fallbackCard(...$card), $cards);
    }

    private function fallbackCard(
        string $id,
        string $name,
        int $hp,
        array $types,
        string $stage,
        ?string $evolvesFrom,
        int $pokedexId,
        array $attacks
    ): array {
        return [
            'id' => $id,
            'name' => $name,
            'image' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$pokedexId}.png",
            'hp' => $hp,
            'types' => $types,
            'supertype' => 'Pokemon',
            'subtypes' => [$stage],
            'stage' => $stage,
            'evolvesFrom' => $evolvesFrom,
            'attacks' => array_map(fn (array $attack) => [
                'name' => $attack[0],
                'cost' => $attack[1],
                'damage' => $attack[2],
                'text' => '',
                'convertedEnergyCost' => count($attack[1]),
            ], $attacks),
            'weaknesses' => [],
            'resistances' => [],
            'retreatCost' => [],
            'rarity' => 'Deck de treino',
            'set' => ['id' => 'fallback', 'name' => 'Arena SENAI', 'series' => 'Batalha TCG'],
        ];
    }
}
