<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Estoque;
use App\Models\Fornecedor;
use App\Models\Produto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // clientes
        Cliente::factory(10)->create();

        // cria um usuário administrativo fixo se ainda não existir
        // evita violação de unicidade em seeds repetidos
        \App\Models\User::firstOrCreate(
            ['email' => 'matheus@confeccao.com'],
            ['name' => 'Confecção Matheus', 'password' => bcrypt('password')]
        );

        // pedidos
        Pedido::factory(10)->create();

        // exemplo fixo pedido
        Pedido::factory()->create([
            'nome' => 'Pedido do Matheus',
            'status' => 'pendente',
        ]);

        // estoque
        Estoque::factory(10)->create();

        // exemplo fixo estoque (usar coluna real `quantidade_estoque`)
        Estoque::factory()->create([
            'nome' => 'Camiseta Básica',
            'quantidade_estoque' => 50, // nome da coluna no migration
        ]);

        // fornecedores
        Fornecedor::factory(10)->create();

        // exemplo fixo de fornecedor
        Fornecedor::factory()->create([
            'nome' => 'Fornecedor Principal',
            'email' => 'fornecedor@gmail.com',
        ]);

        // produtos
        Produto::factory(10)->create();

        // exemplo fixo de produto
        Produto::factory()->create([
            'nome' => 'Camiseta Básica',
            'descricao' => 'Camiseta de algodão confortável',
            'preco' => 49.90,
            'quantidade' => 50,
            'tamanho' => 'M',
            'cor' => 'Preto',
        ]);
    }
}