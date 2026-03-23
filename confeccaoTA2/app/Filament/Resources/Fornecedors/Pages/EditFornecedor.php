<?php

namespace App\Filament\Resources\Fornecedors\Pages;

use App\Filament\Resources\Fornecedors\FornecedorResource;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFornecedor extends EditRecord
{
    protected static string $resource = FornecedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            DeleteAction::make()
                ->label('Excluir fornecedor')
                ->modalHeading('Excluir fornecedor')
                ->modalDescription('Tem certeza que deseja excluir este fornecedor? Essa ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, excluir')
                ->successNotificationTitle('Fornecedor excluído com sucesso!')
                ->color('danger'),
        ];
    }

    // redireciona depois de deletar
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}