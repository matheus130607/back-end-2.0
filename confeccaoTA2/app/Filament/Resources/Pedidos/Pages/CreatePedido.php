<?php

namespace App\Filament\Resources\Pedidos\Pages;

use App\Filament\Resources\Pedidos\PedidoResource;
use App\Filament\Resources\Pedidos\Pages\Concerns\HandlesPedidoAfterSave;
use Filament\Resources\Pages\CreateRecord;

class CreatePedido extends CreateRecord
{
    use HandlesPedidoAfterSave;

    protected static string $resource = PedidoResource::class;

    protected function afterCreate(): void
    {
        $this->finalizarFluxoDoPedido($this->record);
    }
}
