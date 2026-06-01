<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Resources\Permissions\Pages\CreatePermission;
use App\Filament\Resources\Permissions\Pages\EditPermission;
use App\Filament\Resources\Permissions\Pages\ListPermissions;
use App\Filament\Resources\Permissions\Pages\ViewPermission;
use App\Filament\Resources\Permissions\Schemas\PermissionInfolist;
use App\Support\FilamentAccess;
use App\Support\PermissionCatalog;
use BackedEnum;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use UnitEnum;

class PermissionResource extends Resource
{
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

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return FilamentAccess::canAny('permissoes.gerenciar');
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
            Section::make('Permissão')
                ->description('Use nomes amigáveis no painel e mantenha o identificador técnico somente como referência interna.')
                ->schema([
                    Select::make('name')
                        ->label('Permissão')
                        ->options(PermissionCatalog::flatOptions())
                        ->searchable()
                        ->required(),

                    Hidden::make('guard_name')
                        ->default('web'),
                ])
                ->columns(2),
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
                    ->label('Permissão')
                    ->formatStateUsing(fn (string $state): string => PermissionCatalog::labelFor($state))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('module')
                    ->label('Módulo')
                    ->state(fn (Permission $record): string => PermissionCatalog::moduleFor($record->name))
                    ->badge(),

                TextColumn::make('technical_name')
                    ->label('Identificador técnico')
                    ->state(fn (Permission $record): string => $record->name)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('module')
                    ->label('Módulo')
                    ->schema([
                        Select::make('module')
                            ->label('Módulo')
                            ->options(PermissionCatalog::moduleOptions()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $module = $data['module'] ?? null;

                        if (! $module) {
                            return $query;
                        }

                        return $query->whereIn('name', array_keys(PermissionCatalog::DEFINITIONS[$module] ?? []));
                    }),
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
