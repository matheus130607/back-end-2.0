<?php

use App\Http\Controllers\Api\CardsPokemonController;
use App\Http\Controllers\Api\PokedexGridController;
use App\Http\Controllers\BattleCardsController;
use App\Http\Controllers\CustomPokemonController;
use App\Http\Controllers\PokedexController;
use App\Http\Controllers\PokemonGameController;
use App\Http\Controllers\TypeBackgroundController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/pokedex/lista');
})->name('home');

Route::get('/pokedex/lista', [PokedexController::class, 'lista'])->name('pokedex.lista');
Route::get('/pokedex/criar', [CustomPokemonController::class, 'create'])->name('custom-pokemon.create');
Route::post('/pokedex/criar', [CustomPokemonController::class, 'store'])->name('custom-pokemon.store');
Route::get('/pokedex/meus-pokemons', [CustomPokemonController::class, 'index'])->name('custom-pokemon.index');
Route::get('/pokedex/meus-pokemons/{pokemon_id}/editar', [CustomPokemonController::class, 'edit'])
    ->whereNumber('pokemon_id')
    ->name('custom-pokemon.edit');
Route::put('/pokedex/meus-pokemons/{pokemon_id}', [CustomPokemonController::class, 'update'])
    ->whereNumber('pokemon_id')
    ->name('custom-pokemon.update');
Route::delete('/pokedex/meus-pokemons/{pokemon_id}', [CustomPokemonController::class, 'destroy'])
    ->whereNumber('pokemon_id')
    ->name('custom-pokemon.destroy');
Route::get('/pokedex/jogo', [PokemonGameController::class, 'index'])->name('pokedex.game');
Route::get('/pokedex/type-background.svg', TypeBackgroundController::class)->name('type.background');
Route::get('/pokedex', [PokedexController::class, 'random'])->name('pokedex.random');
Route::get('/pokedex/{pokemon}', [PokedexController::class, 'detalhes'])
    ->where('pokemon', '[A-Za-z0-9-]+')
    ->name('pokedex.detalhes');

Route::get('/historia', function () {
    return view('historia-pokemon');
})->name('historia');
Route::get('/historia-pokemon', function () {
    return view('historia-pokemon');
})->name('historia.pokemon');

Route::get('/cartas-pokemon', [CardsPokemonController::class, 'index'])->name('cartas.index');
Route::get('/carta-pokemon/{id}', [CardsPokemonController::class, 'show'])->name('carta.show');
Route::get('/carta-random', [CardsPokemonController::class, 'random'])->name('carta.random');
Route::get('/cartas/set/{setId}', [CardsPokemonController::class, 'bySet'])->name('cartas.bySet');

Route::get('/batalha-cartas', [BattleCardsController::class, 'index'])->name('battle-cards.index');
Route::get('/batalha-cartas/montar-deck', [BattleCardsController::class, 'deckBuilder'])->name('battle-cards.deck');
Route::get('/batalha-cartas/gerar-deck', [BattleCardsController::class, 'autoDeck'])->name('battle-cards.auto');
Route::get('/batalha-cartas/cartas', [BattleCardsController::class, 'cards'])->name('battle-cards.cards');
Route::get('/batalha-cartas/jogar', [BattleCardsController::class, 'play'])->name('battle-cards.play');

Route::get('/pokedex-grid', [PokedexGridController::class, 'index'])->name('pokedex.grid');
Route::get('/api/pokedex-grid', [PokedexGridController::class, 'page'])->name('pokedex.grid.page');
Route::get('/api/pokedex-grid/pokemon/{id}/detalhes', [PokedexGridController::class, 'detalhes'])->name('pokedex.grid.detalhes');
