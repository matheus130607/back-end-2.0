<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\PokemonController;

/*
|--------------------------------------------------------------------------
| API de Exemplo Pokemon 
|--------------------------------------------------------------------------
*/

// ROTA 1 - Controller (VIEW)
Route::get('/pokemon', [PokemonController::class, 'index']);

// ROTA PARA HISTÓRIA POKEMON
Route::get('/historia-pokemon', function () {
    return view('historia-pokemon');
})->name('historia.pokemon');


// ROTA 2 - API externa com nome
Route::get('/pokemon/{nome}', function ($nome) {
    $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$nome}");

    if ($response->successful()) {
        $dados = $response->json();

        return response()->json([
            'status' => 'Conectado com sucesso!',
            'resultado' => [
                'identificador' => $dados['id'],
                'nome_do_pokemon' => ucfirst($dados['name']),
                'foto' => $dados['sprites']['front_default']
            ]
        ], 200);
    }

    return response()->json(['erro' => 'Pokémon não encontrado'], 404);
});


// ROTA 3 - POST
Route::post('/pokemon/novo', function (Request $request) {
    $dados = $request->validate([
        'nome'   => 'required|string|min:3',
        'tipo'   => 'required|string',
        'ataque' => 'required|integer'
    ]);

    return response()->json([
        'mensagem' => 'Pokémon cadastrado com sucesso!',
        'id_gerado' => rand(1000, 9999),
        'dados_recebidos' => $dados
    ], 201);
});