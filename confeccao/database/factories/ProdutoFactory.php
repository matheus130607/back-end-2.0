<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    protected $model = \App\Models\Produto::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->word(),
            'descricao' => fake()->sentence(),
            'preco' => fake()->randomFloat(2, 10, 500),
            'quantidade' => fake()->numberBetween(0, 100),
            'tamanho' => fake()->randomElement(['PP', 'P', 'M', 'G', 'GG']),
            'cor' => fake()->safeColorName(),
        ];
    }
}