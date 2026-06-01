<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            ['nome' => 'Carregador Portátil Powerbank 10.000mAh', 'referencia' => 'PB-10000', 'preco_venda' => 129.90, 'estoque' => 42],
            ['nome' => 'Camiseta Básica 100% Algodão - Preta (M)', 'referencia' => 'CAM-PRETA-M', 'preco_venda' => 59.90, 'estoque' => 120],
            ['nome' => 'Camiseta Básica 100% Algodão - Branca (G)', 'referencia' => 'CAM-BRANCA-G', 'preco_venda' => 59.90, 'estoque' => 96],
            ['nome' => 'Fone Bluetooth Sem Fio', 'referencia' => 'FONE-BT-01', 'preco_venda' => 189.90, 'estoque' => 35],
            ['nome' => 'Mouse Sem Fio', 'referencia' => 'MOUSE-WL-01', 'preco_venda' => 89.90, 'estoque' => 58],
            ['nome' => 'Teclado Mecânico', 'referencia' => 'TECL-MEC-01', 'preco_venda' => 249.90, 'estoque' => 22],
            ['nome' => 'Mochila Executiva', 'referencia' => 'MOCH-EXEC-01', 'preco_venda' => 219.90, 'estoque' => 18],
            ['nome' => 'Garrafa Térmica Inox', 'referencia' => 'GARRAFA-INOX-750', 'preco_venda' => 79.90, 'estoque' => 74],
            ['nome' => 'Cabo USB-C Reforçado', 'referencia' => 'CABO-USBC-2M', 'preco_venda' => 39.90, 'estoque' => 150],
            ['nome' => 'Suporte para Notebook', 'referencia' => 'SUP-NOTE-01', 'preco_venda' => 119.90, 'estoque' => 31],
        ];

        foreach ($produtos as $produto) {
            Produto::updateOrCreate(
                ['referencia' => $produto['referencia']],
                $produto,
            );
        }
    }
}
