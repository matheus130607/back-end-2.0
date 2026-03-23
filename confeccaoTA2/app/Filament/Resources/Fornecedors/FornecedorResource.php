<?php

namespace App\Filament\Resources\Fornecedors;

use App\Filament\Resources\Fornecedors\Pages\CreateFornecedor;
use App\Filament\Resources\Fornecedors\Pages\EditFornecedor;
use App\Filament\Resources\Fornecedors\Pages\ListFornecedors;
use App\Filament\Resources\Fornecedors\Pages\ViewFornecedor;
use App\Filament\Resources\Fornecedors\Schemas\FornecedorForm;
use App\Filament\Resources\Fornecedors\Schemas\FornecedorInfolist;
use App\Filament\Resources\Fornecedors\Tables\FornecedorsTable;
use App\Models\Fornecedor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class FornecedorResource extends Resource
{
    protected static ?string $model = Fornecedor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'fonecedor';

    public static function form(Schema $schema): Schema
    {
        FornecedorForm::configure($schema);
        return $schema
        ->schema([
        TextInput::make('empresa')->required()->label('Nome da empresa'),
        TextInput::make('documento')->email()->label('CNPJ'),
        TextInput::make('endereco')->label('Endereço'),
        TextInput::make('telefone')->tel()->label('Telefone'),

        ]);

    }

    public static function infolist(Schema $schema): Schema
    {
        return FornecedorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return FornecedorsTable::configure($table);
            return $table->columns([
            TextColumn::make('empresa')->searchable(),
            TextColumn::make('documento')->searchable(),
            TextColumn::make('endereco'),
            TextColumn::make('telefone'),
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
            'index' => ListFornecedors::route('/'),
            'create' => CreateFornecedor::route('/create'),
            'view' => ViewFornecedor::route('/{record}'),
            'edit' => EditFornecedor::route('/{record}/edit'),
        ];
    }
}
