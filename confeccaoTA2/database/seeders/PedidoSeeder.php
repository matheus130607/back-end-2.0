<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\EmailPedido;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        EmailPedido::query()->delete();
        ItemPedido::query()->delete();
        Pedido::query()->delete();

        $clientes = Cliente::query()->get()->keyBy('email');
        $produtos = Produto::query()->get()->keyBy('referencia');

        $pedidos = [
            [
                'cliente_email' => 'joao.silva@example.com',
                'status' => Pedido::STATUS_FINALIZADO,
                'created_at' => '2026-05-20 09:15:00',
                'updated_at' => '2026-05-20 15:45:00',
                'itens' => [
                    ['referencia' => 'PB-10000', 'quantidade' => 1],
                    ['referencia' => 'CABO-USBC-2M', 'quantidade' => 2],
                ],
            ],
            [
                'cliente_email' => 'maria.oliveira@example.com',
                'status' => Pedido::STATUS_EM_PRODUCAO,
                'created_at' => '2026-05-22 10:20:00',
                'updated_at' => '2026-05-24 11:05:00',
                'itens' => [
                    ['referencia' => 'CAM-PRETA-M', 'quantidade' => 3],
                    ['referencia' => 'CAM-BRANCA-G', 'quantidade' => 2],
                    ['referencia' => 'GARRAFA-INOX-750', 'quantidade' => 1],
                ],
            ],
            [
                'cliente_email' => 'carlos.santos@example.com',
                'status' => Pedido::STATUS_PENDENTE,
                'created_at' => '2026-05-23 14:30:00',
                'updated_at' => '2026-05-23 14:30:00',
                'itens' => [
                    ['referencia' => 'MOUSE-WL-01', 'quantidade' => 1],
                ],
            ],
            [
                'cliente_email' => 'fernanda.lima@example.com',
                'status' => Pedido::STATUS_FINALIZADO,
                'created_at' => '2026-05-24 08:10:00',
                'updated_at' => '2026-05-25 16:40:00',
                'itens' => [
                    ['referencia' => 'FONE-BT-01', 'quantidade' => 1],
                    ['referencia' => 'SUP-NOTE-01', 'quantidade' => 1],
                    ['referencia' => 'MOCH-EXEC-01', 'quantidade' => 1],
                ],
            ],
            [
                'cliente_email' => 'roberto.almeida@example.com',
                'status' => Pedido::STATUS_CANCELADO,
                'created_at' => '2026-05-25 12:00:00',
                'updated_at' => '2026-05-26 09:30:00',
                'itens' => [
                    ['referencia' => 'TECL-MEC-01', 'quantidade' => 2],
                ],
            ],
            [
                'cliente_email' => 'ana.souza@example.com',
                'status' => Pedido::STATUS_FINALIZADO,
                'created_at' => '2026-05-26 13:25:00',
                'updated_at' => '2026-05-27 10:15:00',
                'itens' => [
                    ['referencia' => 'CAM-PRETA-M', 'quantidade' => 1],
                    ['referencia' => 'CAM-BRANCA-G', 'quantidade' => 1],
                    ['referencia' => 'CABO-USBC-2M', 'quantidade' => 3],
                    ['referencia' => 'GARRAFA-INOX-750', 'quantidade' => 2],
                ],
            ],
            [
                'cliente_email' => 'patricia.gomes@example.com',
                'status' => Pedido::STATUS_EM_PRODUCAO,
                'created_at' => '2026-05-28 09:50:00',
                'updated_at' => '2026-05-30 17:10:00',
                'itens' => [
                    ['referencia' => 'MOCH-EXEC-01', 'quantidade' => 2],
                    ['referencia' => 'SUP-NOTE-01', 'quantidade' => 2],
                ],
            ],
            [
                'cliente_email' => 'lucas.ferreira@example.com',
                'status' => Pedido::STATUS_PENDENTE,
                'created_at' => '2026-05-29 16:45:00',
                'updated_at' => '2026-05-29 16:45:00',
                'itens' => [
                    ['referencia' => 'PB-10000', 'quantidade' => 2],
                    ['referencia' => 'FONE-BT-01', 'quantidade' => 2],
                    ['referencia' => 'MOUSE-WL-01', 'quantidade' => 1],
                    ['referencia' => 'TECL-MEC-01', 'quantidade' => 1],
                    ['referencia' => 'CABO-USBC-2M', 'quantidade' => 4],
                ],
            ],
            [
                'cliente_email' => 'joao.silva@example.com',
                'status' => Pedido::STATUS_FINALIZADO,
                'created_at' => '2026-05-30 11:10:00',
                'updated_at' => '2026-05-31 15:20:00',
                'itens' => [
                    ['referencia' => 'TECL-MEC-01', 'quantidade' => 1],
                    ['referencia' => 'MOUSE-WL-01', 'quantidade' => 1],
                ],
            ],
            [
                'cliente_email' => 'maria.oliveira@example.com',
                'status' => Pedido::STATUS_CANCELADO,
                'created_at' => '2026-05-31 10:05:00',
                'updated_at' => '2026-05-31 12:35:00',
                'itens' => [
                    ['referencia' => 'SUP-NOTE-01', 'quantidade' => 1],
                    ['referencia' => 'GARRAFA-INOX-750', 'quantidade' => 1],
                ],
            ],
            [
                'cliente_email' => 'carlos.santos@example.com',
                'status' => Pedido::STATUS_EM_PRODUCAO,
                'created_at' => '2026-06-01 08:40:00',
                'updated_at' => '2026-06-01 13:55:00',
                'itens' => [
                    ['referencia' => 'CAM-PRETA-M', 'quantidade' => 6],
                    ['referencia' => 'CAM-BRANCA-G', 'quantidade' => 6],
                ],
            ],
            [
                'cliente_email' => 'fernanda.lima@example.com',
                'status' => Pedido::STATUS_PENDENTE,
                'created_at' => '2026-06-01 15:20:00',
                'updated_at' => '2026-06-01 15:20:00',
                'itens' => [
                    ['referencia' => 'CABO-USBC-2M', 'quantidade' => 5],
                ],
            ],
        ];

        foreach ($pedidos as $pedidoData) {
            $createdAt = CarbonImmutable::parse($pedidoData['created_at']);
            $updatedAt = CarbonImmutable::parse($pedidoData['updated_at']);
            $total = 0;

            foreach ($pedidoData['itens'] as $itemData) {
                $produto = $produtos->get($itemData['referencia']);
                $total += (float) $produto->preco_venda * $itemData['quantidade'];
            }

            $pedido = Pedido::create([
                'cliente_id' => $clientes->get($pedidoData['cliente_email'])->id,
                'status' => $pedidoData['status'],
                'valor_total' => $total,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

            foreach ($pedidoData['itens'] as $itemData) {
                $produto = $produtos->get($itemData['referencia']);

                $pedido->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $itemData['quantidade'],
                    'preco_unitario' => $produto->preco_venda,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);
            }
        }
    }
}
