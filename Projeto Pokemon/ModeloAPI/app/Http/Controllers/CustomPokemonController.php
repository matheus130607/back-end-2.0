<?php

namespace App\Http\Controllers;

use App\Models\CustomPokemon;
use App\Support\PokemonTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CustomPokemonController extends Controller
{
    private const FIRST_CUSTOM_ID = 1026;

    public function create()
    {
        return view('pokedex.custom-form', [
            'mode' => 'create',
            'pokemon' => null,
            'nextPokemonId' => $this->nextPokemonId(),
            'types' => PokemonTypes::TYPES,
            'typeColors' => PokemonTypes::colors(),
            'typeLabels' => PokemonTypes::labels(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $pokemonId = $this->nextPokemonId();
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('pokemons/custom', 'public')
            : null;

        $abilities = $this->parseAbilities($data['abilities']);
        $payload = [
            'pokemon_id' => $pokemonId,
            'name' => $data['name'],
            'types' => $data['types'],
            'type' => $data['types'][0],
            'height' => $data['height'],
            'weight' => $data['weight'],
            'ability' => implode(', ', $abilities),
            'image_path' => $imagePath,
        ];

        CustomPokemon::create($this->withOptionalColumns($payload, $data['description'], $abilities));

        return redirect()
            ->route('custom-pokemon.index')
            ->with('success', 'Pokemon criado com sucesso!');
    }

    public function index()
    {
        return view('pokedex.meus-pokemons', [
            'customPokemons' => CustomPokemon::orderBy('pokemon_id')->get(),
            'typeColors' => PokemonTypes::colors(),
        ]);
    }

    public function edit(int $pokemonId)
    {
        $pokemon = $this->findByPokemonId($pokemonId);

        return view('pokedex.custom-form', [
            'mode' => 'edit',
            'pokemon' => $pokemon,
            'nextPokemonId' => $pokemon->pokemon_id,
            'types' => PokemonTypes::TYPES,
            'typeColors' => PokemonTypes::colors(),
            'typeLabels' => PokemonTypes::labels(),
        ]);
    }

    public function update(Request $request, int $pokemonId)
    {
        $pokemon = $this->findByPokemonId($pokemonId);
        $data = $this->validatedData($request);

        $imagePath = $pokemon->image_path;
        if ($request->hasFile('image')) {
            $this->deleteImage($pokemon->image_path);
            $imagePath = $request->file('image')->store('pokemons/custom', 'public');
        }

        $abilities = $this->parseAbilities($data['abilities']);
        $payload = [
            'name' => $data['name'],
            'types' => $data['types'],
            'type' => $data['types'][0],
            'height' => $data['height'],
            'weight' => $data['weight'],
            'ability' => implode(', ', $abilities),
            'image_path' => $imagePath,
        ];

        $pokemon->update($this->withOptionalColumns($payload, $data['description'], $abilities));

        return redirect()
            ->route('custom-pokemon.index')
            ->with('success', 'Pokemon atualizado com sucesso!');
    }

    public function destroy(int $pokemonId)
    {
        $pokemon = $this->findByPokemonId($pokemonId);
        $this->deleteImage($pokemon->image_path);
        $pokemon->delete();

        return redirect()
            ->route('custom-pokemon.index')
            ->with('success', 'Pokemon excluido com sucesso!');
    }

    private function nextPokemonId(): int
    {
        $lastId = (int) CustomPokemon::max('pokemon_id');

        return max(self::FIRST_CUSTOM_ID, $lastId + 1);
    }

    private function findByPokemonId(int $pokemonId): CustomPokemon
    {
        return CustomPokemon::where('pokemon_id', $pokemonId)->firstOrFail();
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'types' => ['required', 'array', 'min:1', 'max:2'],
            'types.*' => ['required', 'string', Rule::in(PokemonTypes::TYPES)],
            'height' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'weight' => ['required', 'numeric', 'min:0.1', 'max:9999'],
            'description' => ['required', 'string', 'max:1000'],
            'abilities' => ['required', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ], [
            'name.required' => 'Informe o nome do Pokemon.',
            'types.required' => 'Escolha pelo menos um tipo.',
            'types.max' => 'Escolha no maximo dois tipos.',
            'height.required' => 'Informe a altura.',
            'height.numeric' => 'A altura deve ser numerica.',
            'weight.required' => 'Informe o peso.',
            'weight.numeric' => 'O peso deve ser numerico.',
            'description.required' => 'Informe uma descricao.',
            'abilities.required' => 'Informe pelo menos uma habilidade.',
            'image.mimes' => 'A imagem deve ser jpg, jpeg, png, webp ou gif.',
            'image.max' => 'A imagem deve ter no maximo 5MB.',
        ]);
    }

    private function parseAbilities(string $abilities): array
    {
        return array_values(array_filter(array_map(
            fn ($ability) => trim($ability),
            explode(',', $abilities)
        ))) ?: ['Habilidade Especial'];
    }

    private function withOptionalColumns(array $payload, string $description, array $abilities): array
    {
        if (Schema::hasColumn('custom_pokemons', 'description')) {
            $payload['description'] = $description;
        }

        if (Schema::hasColumn('custom_pokemons', 'abilities')) {
            $payload['abilities'] = $abilities;
        }

        return $payload;
    }

    private function deleteImage(?string $path): void
    {
        if (!$path || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
