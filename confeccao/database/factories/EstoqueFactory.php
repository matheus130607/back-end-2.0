<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EstoqueFactory extends Factory
{

    protected $model = \App\Models\Estoque::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Nome do produto (ex: Camiseta Básica, Calça Jeans, etc.)
            'nome' => fake()->words(2, true),

            // Pequena descrição opcional
            'descricao' => fake()->sentence(),

            // Preço com 2 casas decimais entre 20 e 500 reais
            'preco' => fake()->randomFloat(2, 20, 500),

            // Quantidade disponível em estoque
            'quantidade_estoque' => fake()->numberBetween(0, 200),

            // Tamanho comum em confecção
            'tamanho' => fake()->randomElement(['PP', 'P', 'M', 'G', 'GG']),

            // Cor do produto
            'cor' => fake()->safeColorName(),
        ];
    }
}
