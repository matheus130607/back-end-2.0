<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PokemonTcgService;
use Illuminate\Http\Request;

class CardsPokemonController extends Controller
{
    public function __construct(private PokemonTcgService $tcg)
    {
    }

    public function index(Request $request)
    {
        $busca = trim((string) $request->input('search', ''));
        $rarity = trim((string) $request->input('rarity', ''));
        $type = trim((string) $request->input('type', ''));
        $set = trim((string) $request->input('set', ''));
        $sort = trim((string) $request->input('sort', ''));
        $page = max(1, (int) $request->input('page', 1));
        $pageSize = 24;

        $data = $this->tcg->searchCards([
            'search' => $busca,
            'rarity' => $rarity,
            'type' => $type,
            'set' => $set,
            'sort' => $sort,
            'page' => $page,
            'pageSize' => $pageSize,
        ]);

        $cards = $data['data'] ?? [];
        $this->sortCardsByPriceIfNeeded($cards, $sort);
        $setOptions = collect($cards)
            ->pluck('set.name')
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $totalCount = (int) ($data['totalCount'] ?? 0);
        $totalPages = max(1, (int) ceil($totalCount / $pageSize));

        return view('cartas-pokemon', compact(
            'cards',
            'busca',
            'rarity',
            'type',
            'set',
            'sort',
            'setOptions',
            'page',
            'totalPages',
            'totalCount'
        ));
    }

    public function show($id)
    {
        $card = $this->tcg->findCard($id);

        if (!$card) {
            return view('carta-detalhe')->with('erro', 'Carta nao encontrada.');
        }

        return view('carta-detalhe', compact('card'));
    }

    public function random()
    {
        $cardId = $this->tcg->randomCardId();

        if (!$cardId) {
            return redirect('/cartas-pokemon')->with('error', 'Nao foi possivel carregar uma carta aleatoria.');
        }

        return redirect("/carta-pokemon/{$cardId}");
    }

    public function bySet(string $setId, Request $request)
    {
        $busca = '';
        $rarity = '';
        $type = '';
        $page = max(1, (int) $request->input('page', 1));
        $pageSize = 12;
        $data = $this->tcg->searchCards([
            'setId' => $setId,
            'page' => $page,
            'pageSize' => $pageSize,
        ]);
        $cards = $data['data'] ?? [];
        $set = $cards[0]['set']['name'] ?? '';
        $sort = '';
        $setOptions = collect($cards)
            ->pluck('set.name')
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $totalCount = (int) ($data['totalCount'] ?? 0);
        $totalPages = max(1, (int) ceil($totalCount / $pageSize));

        return view('cartas-pokemon', compact(
            'cards',
            'busca',
            'rarity',
            'type',
            'set',
            'sort',
            'setOptions',
            'page',
            'totalPages',
            'totalCount'
        ));
    }

    private function sortCardsByPriceIfNeeded(array &$cards, string $sort): void
    {
        if (!in_array($sort, ['price-asc', 'price-desc'], true)) {
            return;
        }

        usort($cards, function (array $a, array $b) use ($sort) {
            $priceA = $this->primaryPriceValue($a) ?? PHP_FLOAT_MAX;
            $priceB = $this->primaryPriceValue($b) ?? PHP_FLOAT_MAX;

            return $sort === 'price-desc'
                ? $priceB <=> $priceA
                : $priceA <=> $priceB;
        });
    }

    private function primaryPriceValue(array $card): ?float
    {
        $candidates = [
            $card['tcgplayer']['prices']['holofoil']['market'] ?? null,
            $card['tcgplayer']['prices']['normal']['market'] ?? null,
            $card['tcgplayer']['prices']['reverseHolofoil']['market'] ?? null,
            $card['cardmarket']['prices']['averageSellPrice'] ?? null,
            $card['cardmarket']['prices']['trendPrice'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_numeric($candidate)) {
                return (float) $candidate;
            }
        }

        return null;
    }
}
