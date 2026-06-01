<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use App\Support\FilamentAccess;
use App\Support\PermissionCatalog;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Cadastros Gerais';
    protected static ?int $navigationSort = 3;

    // Nome que vai aparecer no menu Integral
    protected static ?string $navigationLabel = 'Usuário';

    // Nome singular (ex: usado no botão "Criar Usuário")
    protected static ?string $modelLabel = 'Usuário';

    // Nome plural (ex: usado no título da tabela "Usuários")
    protected static ?string $pluralModelLabel = 'Usuários';

    protected static ?string $recordTitleAttribute = 'Usuários';

    public static function canViewAny(): bool
    {
        return FilamentAccess::canAny('usuarios.gerenciar');
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
            Section::make('Dados do usuário')
                ->schema([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required(),

                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->required(),

                    TextInput::make('password')
                        ->label('Senha')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state): bool => filled($state)),
                ])
                ->columns(3),

            Section::make('Acesso')
                ->schema([
                    Select::make('roles')
                        ->relationship('roles', 'name')
                        ->label('Cargo')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->required(),

                    Select::make('permissions')
                        ->relationship('permissions', 'name')
                        ->label('Permissões adicionais')
                        ->multiple()
                        ->options(fn (): array => PermissionCatalog::groupedPermissionOptions())
                        ->preload()
                        ->searchable()
                        ->helperText('Use apenas para exceções; o ideal é controlar acesso pelos cargos.'),
                ])
                ->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return UsersTable::configure($table);
        return $table
        ->columns([
            
            TextColumn::make('name')
            ->label('Nome')
            ->searchable(),

            TextColumn::make('email')
            ->label('E-mail')
            ->searchable(),

            TextColumn::make('roles.name')
            ->label('Cargo')
            ->badge()
            ->searchable(),

            TextColumn::make('permissions.name')
            ->label('Permissões adicionais')
            ->formatStateUsing(fn (string $state): string => PermissionCatalog::labelFor($state))
            ->badge()
            ->limitList(3)
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
