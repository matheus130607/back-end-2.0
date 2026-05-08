<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PokedexPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_pokedex_pages_load_with_custom_pokemon_database_available(): void
    {
        $this->get('/pokedex/lista')->assertOk();
        $this->get('/pokedex/criar')->assertOk();
        $this->get('/pokedex/meus-pokemons')->assertOk();
    }

    public function test_custom_pokemon_can_be_created(): void
    {
        $response = $this->post('/pokedex/criar', [
            'name' => 'Flamita',
            'types' => ['fire'],
            'height' => 1.2,
            'weight' => 24.5,
            'description' => 'Pokemon criado para teste.',
            'abilities' => 'Brasa, Foco',
        ]);

        $response->assertRedirect(route('custom-pokemon.index'));
        $this->assertDatabaseHas('custom_pokemons', [
            'pokemon_id' => 1026,
            'name' => 'Flamita',
            'type' => 'fire',
        ]);
    }
}
