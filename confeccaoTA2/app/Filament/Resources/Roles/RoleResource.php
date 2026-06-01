<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Pages\ViewRole;
use App\Filament\Resources\Roles\Schemas\RoleInfolist;
use App\Support\FilamentAccess;
use App\Support\PermissionCatalog;
use BackedEnum;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Permissões';
    protected static ?int $navigationSort = 2;

    // Nome que vai aparecer no menu Integral
    protected static ?string $navigationLabel = 'Cargo';

    // Nome singular (ex: usado no botão "Criar Usuário")
    protected static ?string $modelLabel = 'Cargo';

    // Nome plural (ex: usado no título da tabela "Usuários")
    protected static ?string $pluralModelLabel = 'Cargos';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return FilamentAccess::canAny('cargos.gerenciar');
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Section::make('Cargo')
                ->schema([
                    TextInput::make('name')
                        ->label('Nome do cargo')
                        ->required()
                        ->maxLength(255),

                    Hidden::make('guard_name')
                        ->default('web'),
                ])
                ->columns(2),

            Section::make('Permissões por módulo')
                ->description('Selecione apenas o que este cargo pode acessar ou gerenciar.')
                ->schema([
                    Select::make('permissions')
                        ->label('Permissões de acesso')
                        ->relationship('permissions', 'name')
                        ->multiple()
                        ->options(fn (): array => PermissionCatalog::groupedPermissionOptions())
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                ]),
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
                TextColumn::make('name')
                    ->label('Cargo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('permissions_count')
                    ->label('Total de permissões')
                    ->counts('permissions')
                    ->alignCenter(),

                TextColumn::make('permissions.name')
                    ->label('Permissões')
                    ->formatStateUsing(fn (string $state): string => PermissionCatalog::labelFor($state))
                    ->badge()
                    ->limitList(4)
                    ->expandableLimitedList(),
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
