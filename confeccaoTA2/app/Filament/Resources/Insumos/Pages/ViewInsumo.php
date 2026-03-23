<?php

namespace App\Filament\Resources\Insumos\Pages;

use App\Filament\Resources\Insumos\InsumoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInsumo extends ViewRecord
{
    protected static string $resource = InsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
