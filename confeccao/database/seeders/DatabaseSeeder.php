<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            // User::factory(10)->create();
            \App\Models\clientes::Factory(10)->create();

            User::factory()->create([
                'name' => 'Confecção Matheus',
                'email' => 'matheus@confeccao.com',
            ]);
        } catch (\Exception $e) {
            // se o banco não estiver configurado, ignorar silenciosamente
        }
    }
}
