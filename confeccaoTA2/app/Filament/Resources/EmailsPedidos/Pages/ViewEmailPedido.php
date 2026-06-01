<?php

namespace App\Filament\Resources\EmailsPedidos\Pages;

use App\Filament\Resources\EmailsPedidos\EmailPedidoResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailPedido extends ViewRecord
{
    protected static string $resource = EmailPedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('voltar')
                ->label('Voltar')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
