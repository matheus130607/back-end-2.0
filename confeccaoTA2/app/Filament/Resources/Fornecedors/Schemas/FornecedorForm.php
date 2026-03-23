<?php

namespace App\Filament\Resources\Fornecedors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FornecedorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('empresa')
                    ->required(),
                TextInput::make('documento'),
                TextInput::make('endereco')
                    ->required(),
                TextInput::make('telefone')
                    ->tel(),
            ]);
    }
}
