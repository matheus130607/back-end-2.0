<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_secretaria_can_login_and_is_redirected_to_secretaria_dashboard(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'secretaria@safe.com',
            'password' => '123456',
        ]);

        $response->assertRedirect(route('secretaria.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_professor_can_login_and_is_redirected_to_professor_dashboard(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'professor1@safe.com',
            'password' => '123456',
        ]);

        $response->assertRedirect(route('professor.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_portaria_can_login_and_is_redirected_to_portaria_dashboard(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'portaria@safe.com',
            'password' => '123456',
        ]);

        $response->assertRedirect(route('portaria.dashboard'));
        $this->assertAuthenticated();
    }
}
