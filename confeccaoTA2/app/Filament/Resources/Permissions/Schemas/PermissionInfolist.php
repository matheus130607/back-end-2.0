<?php

namespace App\Filament\Resources\Permissions\Schemas;

use App\Support\PermissionCatalog;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Permissão')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Permissão')
                            ->formatStateUsing(fn (string $state): string => PermissionCatalog::labelFor($state)),
                        TextEntry::make('module')
                            ->label('Módulo')
                            ->state(fn ($record): string => PermissionCatalog::moduleFor($record->name))
                            ->badge(),
                        TextEntry::make('technical_name')
                            ->label('Identificador técnico')
                            ->state(fn ($record): string => $record->name),
                    ])
                    ->columns(3),
            ]);
    }
}
