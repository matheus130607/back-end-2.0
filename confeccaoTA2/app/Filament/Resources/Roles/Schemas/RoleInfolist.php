<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Support\PermissionCatalog;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cargo')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Cargo'),
                        TextEntry::make('permissions.name')
                            ->label('Permissões')
                            ->formatStateUsing(fn (string $state): string => PermissionCatalog::labelFor($state))
                            ->badge()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
