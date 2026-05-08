<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomPokemon;
use App\Services\CustomPokemonImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomPokemonController extends Controller
{
    public function __construct(private CustomPokemonImageService $images)
    {
    }

    public function create()
    {
        return view('cadastro-pokemon');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pokemon_id' => 'required|integer|min:1026|max:9999|unique:custom_pokemons,pokemon_id',
            'name' => 'required|string|max:100',
            'types' => 'required|array|min:1|max:2',
            'types.*' => 'string|in:normal,fire,water,electric,grass,ice,fighting,poison,ground,flying,psychic,bug,rock,ghost,dragon,dark,steel,fairy',
            'height' => 'required|numeric|min:0.1|max:100',
            'weight' => 'required|numeric|min:0.1|max:9999',
            'ability' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $data['image_path'] = $request->hasFile('image')
            ? $this->images->storeUploadedImage($request->file('image'))
            : $this->images->generateOrFallback($data);

        $pokemon = CustomPokemon::create([
            'pokemon_id' => $data['pokemon_id'],
            'name' => $data['name'],
            'types' => $data['types'],
            'type' => $data['types'][0],
            'height' => $data['height'],
            'weight' => $data['weight'],
            'ability' => $data['ability'],
            'image_path' => $data['image_path'],
        ]);

        return redirect('/pokedex/' . $pokemon->pokemon_id)
            ->with('success', 'Pokemon criado com sucesso.');
    }

    public function index()
    {
        $customPokemons = CustomPokemon::orderBy('pokemon_id', 'asc')->get();
        $customPokemons->each(function (CustomPokemon $pokemon) {
            $pokemon->types_list = $pokemon->type_list;
        });

        return view('lista-custom-pokemons', compact('customPokemons'));
    }

    public function edit($id)
    {
        $customPokemon = CustomPokemon::find($id);

        if (!$customPokemon) {
            return redirect('/custom-pokemons')->with('error', 'Pokemon nao encontrado.');
        }

        $customPokemon->types_list = $customPokemon->type_list;

        return view('editar-pokemon', compact('customPokemon'));
    }

    public function update(Request $request, $id)
    {
        $customPokemon = CustomPokemon::find($id);

        if (!$customPokemon) {
            return redirect('/custom-pokemons')->with('error', 'Pokemon nao encontrado.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'types' => 'required|array|min:1|max:2',
            'types.*' => 'string|in:normal,fire,water,electric,grass,ice,fighting,poison,ground,flying,psychic,bug,rock,ghost,dragon,dark,steel,fairy',
            'height' => 'required|numeric|min:0.1|max:100',
            'weight' => 'required|numeric|min:0.1|max:9999',
            'ability' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteImage($customPokemon->image_path);
            $data['image_path'] = $this->images->storeUploadedImage($request->file('image'));
        }

        $customPokemon->update([
            'name' => $data['name'],
            'types' => $data['types'],
            'type' => $data['types'][0],
            'height' => $data['height'],
            'weight' => $data['weight'],
            'ability' => $data['ability'],
            'image_path' => $data['image_path'] ?? $customPokemon->image_path,
        ]);

        return redirect('/pokedex/' . $customPokemon->pokemon_id)
            ->with('success', 'Pokemon atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $customPokemon = CustomPokemon::find($id);

        if (!$customPokemon) {
            return redirect('/custom-pokemons')->with('error', 'Pokemon nao encontrado.');
        }

        $name = $customPokemon->name;
        $this->deleteImage($customPokemon->image_path);
        $customPokemon->delete();

        return redirect('/custom-pokemons')->with('success', 'Pokemon "' . $name . '" removido com sucesso.');
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
