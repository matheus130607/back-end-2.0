<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Support\PermissionCatalog;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Usuário')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nome'),
                        TextEntry::make('email')
                            ->label('E-mail'),
                        TextEntry::make('roles.name')
                            ->label('Cargos')
                            ->badge(),
                        TextEntry::make('permissions.name')
                            ->label('Permissões adicionais')
                            ->formatStateUsing(fn (string $state): string => PermissionCatalog::labelFor($state))
                            ->badge(),
                    ])
                    ->columns(2),
            ]);
    }
}
