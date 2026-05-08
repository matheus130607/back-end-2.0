<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PokemonCardsTest extends TestCase
{
    public function test_random_card_redirects_using_local_fallback_when_tcg_api_fails(): void
    {
        Http::fake([
            'api.pokemontcg.io/*' => Http::response([], 500),
        ]);

        $response = $this->get('/carta-random');

        $response->assertRedirect();
        $this->assertStringStartsWith(
            '/carta-pokemon/',
            parse_url((string) $response->headers->get('Location'), PHP_URL_PATH)
        );
    }
}
