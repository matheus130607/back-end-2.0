<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pedido>
 */
class PedidoFactory extends Factory
{
    protected $model = \App\Models\Pedido::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'numero' => fake()->numberBetween(10000, 99999),
            'preco' => fake()->randomFloat(2, 10, 2000), // 2 casas decimais, entre 10 e 2000
            'status' => fake()->randomElement([
                'pendente',
                'pago',
                'cancelado'
            ]),
            'quantidade' => fake()->numberBetween(1, 50),
        ];
    }
}
