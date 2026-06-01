<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public const DEFAULT_PASSWORD = 'password';

    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrador Sistema',
                'email' => 'admin@sistema.com',
                'role' => 'Administrador',
            ],
            [
                'name' => 'Gerente Vendas',
                'email' => 'gerente@sistema.com',
                'role' => 'Gerente',
            ],
            [
                'name' => 'Vendedor Sistema',
                'email' => 'vendedor@sistema.com',
                'role' => 'Vendedor',
            ],
            [
                'name' => 'Usuário Teste',
                'email' => 'usuario@sistema.com',
                'role' => 'Usuário comum',
            ],
            [
                'name' => 'Cliente Teste',
                'email' => 'cliente@sistema.com',
                'role' => 'Cliente',
            ],
            [
                'name' => 'Fornecedor Teste',
                'email' => 'fornecedor@sistema.com',
                'role' => 'Fornecedor',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make(self::DEFAULT_PASSWORD),
                ],
            );

            $user->syncRoles([$userData['role']]);
            $user->syncPermissions([]);
        }
    }
}
