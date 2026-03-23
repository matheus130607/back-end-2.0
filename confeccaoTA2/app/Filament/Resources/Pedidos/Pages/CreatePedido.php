<?php

namespace App\Filament\Resources\Pedidos\Pages;

use App\Filament\Resources\Pedidos\PedidoResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;

    // Esta função roda assim que o pedido é salvo no banco
    protected function afterCreate(): void {
        $pedido = $this->record;

        // Pega todos os itens desse pedido, multiplica quantidade x preço e soma tudo
        $total = $pedido->itens->sum(function ($item) {
            return $item->quantidade * $item->preco_unitario;
        });

        //Atualiza o valor total do pedido e salva
        $pedido->update(['valor_total' => $total]);
    }
}
