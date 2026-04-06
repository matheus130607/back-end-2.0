<?php

namespace App\Filament\Resources\Clientes;

use App\Filament\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Resources\Clientes\Pages\EditCliente;
use App\Filament\Resources\Clientes\Pages\ListClientes;
use App\Filament\Resources\Clientes\Pages\ViewCliente;
use App\Filament\Resources\Clientes\Schemas\ClienteForm;
use App\Filament\Resources\Clientes\Schemas\ClienteInfolist;
use App\Filament\Resources\Clientes\Tables\ClientesTable;
use App\Models\Cliente;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\RawJs;


class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|UnitEnum|null $navigationGroup = 'Cadastros Gerais';
    protected static ?int $navigationSort = 1;

    // Nome que vai aparecer no menu Integral
    protected static ?string $navigationLabel = 'Cliente';

    // Nome singular (ex: usado no botão "Criar Usuário")
    protected static ?string $modelLabel = 'Cliente';

    // Nome plural (ex: usado no título da tabela "Usuários")
    protected static ?string $pluralModelLabel = 'Clientes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Cliente';

    public static function form(Schema $schema): Schema
    {
        ClienteForm::configure($schema);
        return $schema
        ->schema([
            TextInput::make('nome')->required()->label('Nome Completo'),
            TextInput::make('email')->email()->label('Email'),
            TextInput::make('telefone')->tel()->label('Telefone/Zap')->mask('(99) 99999-9999'),

            TextInput::make('documento')->label('CPF ou CNPJ'),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClienteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return ClientesTable::configure($table);
        return $table
        ->columns([
            TextColumn::make('nome')->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('telefone'),
            TextColumn::make('documento'),
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
            'index' => ListClientes::route('/'),
            'create' => CreateCliente::route('/create'),
            'view' => ViewCliente::route('/{record}'),
            'edit' => EditCliente::route('/{record}/edit'),
        ];
    }
}
