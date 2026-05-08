<?php

namespace App\Http\Controllers;

use App\Models\CustomPokemon;
use App\Support\PokemonTypes;

class PokedexController extends Controller
{
    private const MAX_OFFICIAL_POKEMON = 1025;

    public function lista()
    {
        return view('pokedex.lista', [
            'typeColors' => PokemonTypes::colors(),
            'typeLabels' => PokemonTypes::labels(),
            'typeNames' => PokemonTypes::TYPES,
            'customPokemons' => CustomPokemon::orderBy('pokemon_id')
                ->get()
                ->map(fn (CustomPokemon $pokemon) => $this->customPokemonPayload($pokemon))
                ->values(),
        ]);
    }

    public function random()
    {
        return redirect()->route('pokedex.detalhes', ['pokemon' => random_int(1, self::MAX_OFFICIAL_POKEMON)]);
    }

    public function detalhes(string $pokemon)
    {
        $isNumeric = ctype_digit($pokemon);
        $numericId = $isNumeric ? (int) $pokemon : null;
        $customPokemon = $numericId && $numericId > self::MAX_OFFICIAL_POKEMON
            ? CustomPokemon::where('pokemon_id', $numericId)->first()
            : null;

        return view('pokedex.detalhes', [
            'pokemonKey' => $pokemon,
            'typeColors' => PokemonTypes::colors(),
            'typeLabels' => PokemonTypes::labels(),
            'typeNames' => PokemonTypes::TYPES,
            'customPokemon' => $customPokemon ? $this->customPokemonPayload($customPokemon) : null,
        ]);
    }

    private function customPokemonPayload(CustomPokemon $pokemon): array
    {
        $types = PokemonTypes::normalizeMany($pokemon->type_list);

        return [
            'id' => (int) $pokemon->pokemon_id,
            'pokemon_id' => (int) $pokemon->pokemon_id,
            'name' => $pokemon->name,
            'types' => $types,
            'height' => (float) $pokemon->height,
            'weight' => (float) $pokemon->weight,
            'description' => $pokemon->description ?: 'Descricao nao disponivel.',
            'abilities' => $pokemon->ability_list,
            'image' => $pokemon->public_image_url,
            'sprite' => $pokemon->public_image_url,
            'generation' => 'created',
            'isCustom' => true,
            'detailUrl' => route('pokedex.detalhes', ['pokemon' => $pokemon->pokemon_id]),
            'editUrl' => route('custom-pokemon.edit', ['pokemon_id' => $pokemon->pokemon_id]),
        ];
    }
}
