<?php

namespace App\Filament\Resources\Fornecedors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FornecedorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('empresa'),
                TextEntry::make('documento')
                    ->placeholder('-'),
                TextEntry::make('endereco'),
                TextEntry::make('telefone')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
