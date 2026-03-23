<?php

namespace App\Filament\Resources\Pedidos\Pages;

use App\Filament\Resources\Pedidos\PedidoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPedido extends EditRecord
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
     
    // Esta função roda toda vez que você edita e salva o pedido
    protected function afterSave(): void
    {
        $pedido = $this->record;
        
        $total = $pedido->itens->sum(function ($item) {
            return $item->quantidade * $item->preco_unitario;
        });

        $pedido->update(['valor_total' => $total]);
    }
}
