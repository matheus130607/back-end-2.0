<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            DeleteAction::make()
                ->label('Excluir cliente')
                ->modalHeading('Excluir cliente')
                ->modalDescription('Tem certeza que deseja excluir este fornecedor? Essa ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, excluir')
                ->successNotificationTitle('Cliente excluído com sucesso!')
                ->color('danger'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResuorce()::getUrl('index');
    }
}
