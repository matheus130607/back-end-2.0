<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PokeApiService;
use Illuminate\Http\Request;

class PokedexGridController extends Controller
{
    public function __construct(private PokeApiService $pokeApi)
    {
    }

    public function index(Request $request)
    {
        $offset = max(0, (int) $request->input('offset', 0));
        $limit = 36;
        $data = $this->pokeApi->listPokemon($offset, $limit);
        $pokemons = $data['pokemons'];
        $totalCount = $data['totalCount'];

        return view('pokedex-grid', compact('pokemons', 'offset', 'limit', 'totalCount'));
    }

    public function page(Request $request)
    {
        $offset = max(0, (int) $request->input('offset', 0));
        $limit = max(12, min(60, (int) $request->input('limit', 36)));
        $data = $this->pokeApi->listPokemon($offset, $limit);

        return response()->json([
            'pokemons' => $data['pokemons'],
            'offset' => $offset,
            'limit' => $limit,
            'totalCount' => $data['totalCount'],
            'hasMore' => ($offset + $limit) < $data['totalCount'],
        ]);
    }

    public function detalhes($id)
    {
        $details = $this->pokeApi->details($id);

        if (!$details) {
            return response()->json(['erro' => 'Pokemon nao encontrado'], 404);
        }

        return response()->json($details);
    }
}
