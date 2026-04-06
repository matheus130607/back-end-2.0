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
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

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

    public static function form(Schema $schema): Schema
    {
        // return UserForm::configure($schema);
        return $schema
        ->schema([

            TextInput::make('name')
            ->label('Nome')
            ->required(),

            TextInput::make('email')
            ->label('E-mail')
            ->required(),

            TextInput::make('password')
            ->label('Senha')
            ->password()
            ->revealable()
            ->required(),

            Select::make('permissions')
            ->relationship('permissions', 'name')
            ->label('Permissão')
            ->required(),

            Select::make('roles')
            ->relationship('roles', 'name')
            ->label('Cargo')
            ->required(),
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

            TextColumn::make('password')
            ->label('Senha')
            ->formatStateUsing(fn () => '********')
            ->searchable(),

            TextColumn::make('permissios.name')
            ->label('Permissão')
            ->searchable(),

            TextColumn::make('roles.name')
            ->label('Cargo')
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
