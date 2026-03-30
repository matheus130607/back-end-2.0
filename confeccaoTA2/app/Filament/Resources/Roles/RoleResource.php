<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Pages\ViewRole;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Schemas\RoleInfolist;
use App\Filament\Resources\Roles\Tables\RolesTable;
use Spatie\Permission\Models\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class RoleResource extends Resource
{

    // public static function canAccess(): bool {

    //     return auth()->user()?->hasRole('Admin') ?? false;
    // }

    public static function canAccess(): bool {
        return auth()->user()?->can('acessar_clientes') ?? false;
        

    }


    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Cargos e funções';

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Select::make('permissions')
                ->label('Permissões de Acesso')
                ->multiple()
                ->relationship('permissions', 'name')
                ->preload()
                ->columnSpanFull(),

            TextInput::make('name')
                ->label('Nome da Regra')
                ->required(),

            
        ]);      
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
    
        return $table
            ->columns([

                TextColumn::make('permissions.name')
                    ->label('Permissões de Acesso')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nome da Regra')
                    ->searchable(),
            ]);

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
