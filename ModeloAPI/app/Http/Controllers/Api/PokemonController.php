<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PokemonController extends Controller
{
    public function index(Request $request)
    {
        // PRIORIDADE 1: Verifica se veio uma busca por nome
        $busca = $request->input('pokemon');
        
        if ($busca) {
            $nomeOuId = strtolower($busca);
            $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$nomeOuId}");
            
            if ($response->successful()) {
                $pokemon = $response->json();
                return view('pokemon', compact('pokemon'));
            } else {
                // Se não encontrar o Pokémon buscado, retorna erro
                return view('pokemon', ['erro' => "Pokémon '{$busca}' não encontrado!"]);
            }
        }
        
        // PRIORIDADE 2: Verifica se veio um ID específico
        $id = $request->input('id');
        
        if ($id) {
            $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$id}");
            
            if ($response->successful()) {
                $pokemon = $response->json();
                return view('pokemon', compact('pokemon'));
            }
        }
        
        // PRIORIDADE 3: Se não veio nada, gera um Pokémon aleatório
        $idAleatorio = rand(1, 1025);
        $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$idAleatorio}");
        
        if ($response->successful()) {
            $pokemon = $response->json();
            return view('pokemon', compact('pokemon'));
        }
        
        return "Erro ao buscar Pokémon";
    }
}