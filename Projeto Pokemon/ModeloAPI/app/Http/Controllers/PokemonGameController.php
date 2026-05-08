<?php

namespace App\Http\Controllers;

use App\Services\PokemonTcgService;
use App\Support\PokemonTypes;
use Illuminate\Http\Request;

class PokemonGameController extends Controller
{
    public function __construct(private PokemonTcgService $tcg)
    {
    }

    public function index(Request $request)
    {
        $playerTypes = PokemonTypes::normalizeMany(explode(',', (string) $request->input('types', 'fire,electric')));
        $botTypes = PokemonTypes::normalizeMany(['water', 'grass']);
        $playerDeck = $this->tcg->battleDeck($playerTypes);
        $botDeck = $this->tcg->battleDeck($botTypes);
        $backgroundUrl = PokemonTypes::typeBackgroundUrl($playerTypes);

        return view('pokedex-game', compact('playerDeck', 'botDeck', 'playerTypes', 'botTypes', 'backgroundUrl'));
    }
}
