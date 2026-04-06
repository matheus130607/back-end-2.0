<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Resources\Permissions\Pages\CreatePermission;
use App\Filament\Resources\Permissions\Pages\EditPermission;
use App\Filament\Resources\Permissions\Pages\ListPermissions;
use App\Filament\Resources\Permissions\Pages\ViewPermission;
use App\Filament\Resources\Permissions\Schemas\PermissionForm;
use App\Filament\Resources\Permissions\Schemas\PermissionInfolist;
use App\Filament\Resources\Permissions\Tables\PermissionsTable;
use Spatie\Permission\Models\Permission;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class PermissionResource extends Resource
{
    // public static function canAccess(): bool {

    //     return auth()->user()?->hasRole('Admin') ?? false;
    // }

    // public static function canAccess(): bool {
    //     return auth()->user()?->can('acessar_clientes') ?? false;
    // }

    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Permissões';
    protected static ?int $navigationSort = 1;

    // Nome que vai aparecer no menu Integral
    protected static ?string $navigationLabel = 'Permissão';

    // Nome singular (ex: usado no botão "Criar Usuário")
    protected static ?string $modelLabel = 'Permissão';

    // Nome plural (ex: usado no título da tabela "Usuários")
    protected static ?string $pluralModelLabel = 'Permissões';

    protected static ?string $recordTitleAttribute = 'Permissões';

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->schema([
            TextInput::make('name')
                ->label('Nome da Regra')
                ->required(),

            TextInput::make('guard_name')
                ->label('Sigla da Regra'),

        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Nome da Regra')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guard_name')
                    ->label('Sigla da Regra')
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
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'view' => ViewPermission::route('/{record}'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
