<?php

namespace App\Filament\Resources\Produtos;

use App\Filament\Resources\Produtos\Pages\CreateProduto;
use App\Filament\Resources\Produtos\Pages\EditProduto;
use App\Filament\Resources\Produtos\Pages\ListProdutos;
use App\Filament\Resources\Produtos\Pages\ViewProduto;
use App\Filament\Resources\Produtos\Schemas\ProdutoForm;
use App\Filament\Resources\Produtos\Schemas\ProdutoInfolist;
use App\Filament\Resources\Produtos\Tables\ProdutosTable;
use App\Models\Produto;
use App\Support\FilamentAccess;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ProdutoResource extends Resource
{
    protected static ?string $model = Produto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Estoques';
    protected static ?int $navigationSort = 1;

    // Nome que vai aparecer no menu Integral
    protected static ?string $navigationLabel = 'Produto';

    // Nome singular (ex: usado no botão "Criar Usuário")
    protected static ?string $modelLabel = 'Produto';

    // Nome plural (ex: usado no título da tabela "Usuários")
    protected static ?string $pluralModelLabel = 'Produtos';

    protected static ?string $recordTitleAttribute = 'Produto';

    public static function canViewAny(): bool
    {
        return FilamentAccess::canAny(['produtos.gerenciar', 'estoque.visualizar']);
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return FilamentAccess::canAny('produtos.gerenciar');
    }

    public static function canEdit(Model $record): bool
    {
        return FilamentAccess::canAny('produtos.gerenciar');
    }

    public static function canDelete(Model $record): bool
    {
        return FilamentAccess::canAny('produtos.gerenciar');
    }

    public static function canDeleteAny(): bool
    {
        return FilamentAccess::canAny('produtos.gerenciar');
    }

    public static function form(Schema $schema): Schema
    {
        return ProdutoForm::configure($schema);
        return $schema->schema([
            TextInput::make('nome')->required()->label('Nome do Produto'),
            TextInput::make('referencia')->label('Código/Referência'),
            TextInput::make('preco_venda')->numeric()->prefix('R$')->label('Preço de Venda'),
            TextInput::make('estoque')->numeric()->default(0),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProdutoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProdutosTable::configure($table);
        return $table->columns([
            TextColumn::make('referencia')->searchable(),
            TextColumn::make('nome')->searchable(),
            TextColumn::make('preco_venda')->money('BRL'),
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
            'index' => ListProdutos::route('/'),
            'create' => CreateProduto::route('/create'),
            'view' => ViewProduto::route('/{record}'),
            'edit' => EditProduto::route('/{record}/edit'),
        ];
    }
}
