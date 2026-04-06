<?php

namespace App\Filament\Resources\Insumos;

use App\Filament\Resources\Insumos\Pages\CreateInsumo;
use App\Filament\Resources\Insumos\Pages\EditInsumo;
use App\Filament\Resources\Insumos\Pages\ListInsumos;
use App\Filament\Resources\Insumos\Pages\ViewInsumo;
use App\Filament\Resources\Insumos\Schemas\InsumoForm;
use App\Filament\Resources\Insumos\Schemas\InsumoInfolist;
use App\Filament\Resources\Insumos\Tables\InsumosTable;
use App\Models\Insumo;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class InsumoResource extends Resource
{
    protected static ?string $model = Insumo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Estoques';
    protected static ?int $navigationSort = 3;

    // Nome que vai aparecer no menu Integral
    protected static ?string $navigationLabel = 'Insumo';

    // Nome singular (ex: usado no botão "Criar Usuário")
    protected static ?string $modelLabel = 'Insumo';

    // Nome plural (ex: usado no título da tabela "Usuários")
    protected static ?string $pluralModelLabel = 'Insumos';

    protected static ?string $recordTitleAttribute = 'Insumo';

    public static function form(Schema $schema): Schema
    {
        
        return $schema->schema([
            TextInput::make('nome')->required(),
            TextInput::make('unidade_medida')->required()->label('Unidade (Kg, Metros, Un...)'),
            TextInput::make('preco_custo')->numeric()->prefix('R$')->label('Preço de Custo'),
            TextInput::make('estoque')->numeric()->default(0),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InsumoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        
    
        return $table->columns([
            TextColumn::make('nome')->searchable(),
            TextColumn::make('unidade_medida'),
            TextColumn::make('preco_custo')->money('BRL'),
            TextColumn::make('estoque'),
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
            'index' => ListInsumos::route('/'),
            'create' => CreateInsumo::route('/create'),
            'view' => ViewInsumo::route('/{record}'),
            'edit' => EditInsumo::route('/{record}/edit'),
        ];
    }
}
