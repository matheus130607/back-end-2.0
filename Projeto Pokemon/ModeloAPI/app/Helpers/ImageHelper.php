<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Obtém URL da imagem de alta qualidade do Pokémon
     */
    public static function getPokemonImage($id, $style = 'official_artwork')
    {
        $styles = [
            'official_artwork' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$id}.png",
            'home' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/{$id}.png",
            'dream_world' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/dream-world/{$id}.svg",
            'showdown' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/showdown/{$id}.gif",
            'front_default' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png",
        ];
        
        return $styles[$style] ?? $styles['official_artwork'];
    }
    
    /**
     * Tenta carregar imagem, com fallback
     */
    public static function getImageWithFallback($id)
    {
        $urls = [
            "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$id}.png",
            "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/{$id}.png",
            "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png",
        ];
        
        return $urls[0]; // Retorna a primeira URL, o fallback será feito no front-end
    }
}