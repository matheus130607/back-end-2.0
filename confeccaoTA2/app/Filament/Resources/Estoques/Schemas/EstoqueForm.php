<?php

namespace App\Filament\Resources\Estoques\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EstoqueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('produto_id')
                    ->required()
                    ->numeric(),
                TextInput::make('quantidade')
                    ->required()
                    ->numeric(),
                TextInput::make('tipo_movimentacao')
                    ->required()
                    ->default('Saída'),
                TextInput::make('observacao')
                    ->required(),
            ]);
    }
}
