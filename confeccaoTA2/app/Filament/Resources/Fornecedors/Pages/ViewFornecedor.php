<?php

namespace App\Filament\Resources\Fornecedors\Pages;

use App\Filament\Resources\Fornecedors\FornecedorResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFornecedor extends ViewRecord
{
    protected static string $resource = FornecedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
