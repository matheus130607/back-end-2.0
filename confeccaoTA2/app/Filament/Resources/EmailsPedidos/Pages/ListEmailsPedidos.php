<?php

namespace App\Filament\Resources\EmailsPedidos\Pages;

use App\Filament\Resources\EmailsPedidos\EmailPedidoResource;
use Filament\Resources\Pages\ListRecords;

class ListEmailsPedidos extends ListRecords
{
    protected static string $resource = EmailPedidoResource::class;
}
