<?php

namespace App\Filament\Resources\Insumos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InsumoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nome'),
                TextEntry::make('unidade_medida'),
                TextEntry::make('preco_custo')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('estoque')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
