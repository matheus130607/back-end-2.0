<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FornecedorFactory extends Factory
{
    protected $model = \App\Models\Fornecedor::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cnpj' => fake()->numerify('##############'),
            'telefone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'empresa' => fake()->company(),
        ];
    }
}