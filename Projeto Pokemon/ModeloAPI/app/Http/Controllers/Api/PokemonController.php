<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\CustomPokemon;
use Illuminate\Support\Facades\DB;

class PokemonController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('pokemon');
        
        if ($busca) {
            try {
                $customPokemon = CustomPokemon::where('name', 'like', "%{$busca}%")->first();
                
                if ($customPokemon) {
                    $pokemon = $this->formatCustomPokemonData($customPokemon);
                    return view('pokemon', compact('pokemon'));
                }
            } catch (\Exception $e) {
                // Ignorar
            }
            
            $nomeOuId = strtolower($busca);
            $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$nomeOuId}");
            
            if ($response->successful()) {
                $pokemon = $response->json();
                $pokemon = $this->addHighQualityImages($pokemon);
                $pokemon = $this->addPokemonCry($pokemon);
                return view('pokemon', compact('pokemon'));
            } else {
                return view('pokemon', ['erro' => "Pokémon '{$busca}' não encontrado!"]);
            }
        }
        
        $id = $request->input('id');
        
        if ($id) {
            try {
                $customPokemon = CustomPokemon::where('pokemon_id', $id)->first();
                
                if ($customPokemon) {
                    $pokemon = $this->formatCustomPokemonData($customPokemon);
                    return view('pokemon', compact('pokemon'));
                }
            } catch (\Exception $e) {
                // Ignorar
            }
            
            $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$id}");
            
            if ($response->successful()) {
                $pokemon = $response->json();
                $pokemon = $this->addHighQualityImages($pokemon);
                $pokemon = $this->addPokemonCry($pokemon);
                return view('pokemon', compact('pokemon'));
            } else {
                return view('pokemon', ['erro' => "Pokémon com ID '{$id}' não encontrado!"]);
            }
        }
        
        try {
            $customPokemon = CustomPokemon::inRandomOrder()->first();
            
            if ($customPokemon) {
                $pokemon = $this->formatCustomPokemonData($customPokemon);
                return view('pokemon', compact('pokemon'));
            }
        } catch (\Exception $e) {
            // Ignorar
        }
        
        $idAleatorio = rand(1, 1025);
        $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$idAleatorio}");
        
        if ($response->successful()) {
            $pokemon = $response->json();
            $pokemon = $this->addHighQualityImages($pokemon);
            $pokemon = $this->addPokemonCry($pokemon);
            return view('pokemon', compact('pokemon'));
        }
        
        return view('pokemon', ['erro' => "Erro ao buscar Pokémon. Tente novamente!"]);
    }
    
    /**
     * Adiciona URLs de imagens de alta qualidade (incluindo Shiny)
     */
    private function addHighQualityImages($pokemon)
    {
        $id = $pokemon['id'];
        
        // Imagens normais
        $pokemon['sprites']['official_artwork'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$id}.png";
        $pokemon['sprites']['home'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/{$id}.png";
        $pokemon['sprites']['dream_world'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/dream-world/{$id}.svg";
        $pokemon['sprites']['showdown'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/showdown/{$id}.gif";
        
        // Imagens Shiny (variante brilhante)
        $pokemon['sprites']['official_artwork_shiny'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/shiny/{$id}.png";
        $pokemon['sprites']['home_shiny'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/shiny/{$id}.png";
        $pokemon['sprites']['showdown_shiny'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/showdown/shiny/{$id}.gif";
        
        // URLs alternativas (fallback)
        $pokemon['sprites']['front_shiny'] = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/shiny/{$id}.png";
        
        return $pokemon;
    }
    
    /**
     * Adiciona o som (cry) do Pokémon
     */
    private function addPokemonCry($pokemon)
    {
        $id = $pokemon['id'];
        
        // URLs dos cries do Pokémon (diferentes versões)
        $pokemon['cries'] = [
            'latest' => "https://raw.githubusercontent.com/PokeAPI/cries/main/cries/pokemon/latest/{$id}.ogg",
            'legacy' => "https://raw.githubusercontent.com/PokeAPI/cries/main/cries/pokemon/legacy/{$id}.ogg"
        ];
        
        return $pokemon;
    }
    
    private function formatCustomPokemonData($customPokemon)
    {
        $typeColors = [
            'normal' => '#A8A878', 'fire' => '#F08030', 'water' => '#6890F0',
            'electric' => '#F8D030', 'grass' => '#78C850', 'ice' => '#98D8D8',
            'fighting' => '#C03028', 'poison' => '#A040A0', 'ground' => '#E0C068',
            'flying' => '#A890F0', 'psychic' => '#F85888', 'bug' => '#A8B820',
            'rock' => '#B8A038', 'ghost' => '#705898', 'dragon' => '#7038F8',
            'dark' => '#705848', 'steel' => '#B8B8D0', 'fairy' => '#EE99AC'
        ];
        
        $types = [];
        
        if (isset($customPokemon->types)) {
            if (is_string($customPokemon->types)) {
                $types = json_decode($customPokemon->types, true);
            } elseif (is_array($customPokemon->types)) {
                $types = $customPokemon->types;
            }
        }
        
        if (empty($types) && isset($customPokemon->type)) {
            $types = [$customPokemon->type];
        }
        
        if (empty($types)) {
            $types = ['normal'];
        }
        
        $height = isset($customPokemon->height) ? $customPokemon->height * 10 : 100;
        $weight = isset($customPokemon->weight) ? $customPokemon->weight * 10 : 500;
        $ability = isset($customPokemon->ability) && $customPokemon->ability 
            ? $customPokemon->ability 
            : 'Habilidade Especial';
        
        $imageUrl = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png';
        
        if (isset($customPokemon->image_path) && $customPokemon->image_path) {
            if (filter_var($customPokemon->image_path, FILTER_VALIDATE_URL)) {
                $imageUrl = $customPokemon->image_path;
            } else {
                $imageUrl = asset('storage/' . $customPokemon->image_path);
            }
        }
        
        $formattedTypes = [];
        foreach ($types as $type) {
            $formattedTypes[] = [
                'type' => [
                    'name' => $type,
                    'url' => null
                ]
            ];
        }
        
        return [
            'id' => $customPokemon->pokemon_id,
            'name' => $customPokemon->name,
            'height' => $height,
            'weight' => $weight,
            'types' => $formattedTypes,
            'abilities' => [
                ['ability' => ['name' => $ability]]
            ],
            'stats' => [
                ['stat' => ['name' => 'hp'], 'base_stat' => 100],
                ['stat' => ['name' => 'attack'], 'base_stat' => 100],
                ['stat' => ['name' => 'defense'], 'base_stat' => 100],
                ['stat' => ['name' => 'special-attack'], 'base_stat' => 100],
                ['stat' => ['name' => 'special-defense'], 'base_stat' => 100],
                ['stat' => ['name' => 'speed'], 'base_stat' => 100]
            ],
            'sprites' => [
                'front_default' => $imageUrl,
                'official_artwork' => $imageUrl,
                'other' => [
                    'official-artwork' => [
                        'front_default' => $imageUrl
                    ]
                ]
            ],
            'is_custom' => true,
            'type_color' => $typeColors[$types[0]] ?? '#68A090',
            'created_at' => $customPokemon->created_at ?? null,
            'updated_at' => $customPokemon->updated_at ?? null
        ];
    }
}