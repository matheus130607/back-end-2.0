<?php

namespace App\Filament\Resources\Pedidos;


use App\Filament\Resources\Pedidos\Pages\CreatePedido;
use App\Filament\Resources\Pedidos\Pages\EditPedido;
use App\Filament\Resources\Pedidos\Pages\ListPedidos;
use App\Filament\Resources\Pedidos\Pages\ViewPedido;
use App\Filament\Resources\Pedidos\Schemas\PedidoInfolist;
use App\Filament\Resources\Pedidos\Tables\PedidosTable;
use App\Models\Pedido;
use App\Models\Produto;
use App\Support\FilamentAccess;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use UnitEnum;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 1;

    // Nome que vai aparecer no menu Integral
    protected static ?string $navigationLabel = 'Pedido';

    // Nome singular (ex: usado no botão "Criar Usuário")
    protected static ?string $modelLabel = 'Pedido';

    // Nome plural (ex: usado no título da tabela "Usuários")
    protected static ?string $pluralModelLabel = 'Pedidos';

    protected static ?string $recordTitleAttribute = 'Pedido';

    public static function canViewAny(): bool
    {
        return FilamentAccess::canAny(['pedidos.gerenciar', 'pedidos.criar', 'pedidos.editar']);
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return FilamentAccess::canAny(['pedidos.gerenciar', 'pedidos.criar']);
    }

    public static function canEdit(Model $record): bool
    {
        return FilamentAccess::canAny(['pedidos.gerenciar', 'pedidos.editar']);
    }

    public static function canDelete(Model $record): bool
    {
        return FilamentAccess::canAny('pedidos.gerenciar');
    }

    public static function canDeleteAny(): bool
    {
        return FilamentAccess::canAny('pedidos.gerenciar');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Dados do pedido')
                    ->schema([
                        Select::make('cliente_id')
                            ->relationship('cliente', 'nome')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Cliente'),

                        Select::make('status')
                            ->options(Pedido::statusOptions())
                            ->default(Pedido::STATUS_PENDENTE)
                            ->required()
                            ->label('Status'),

                        TextInput::make('valor_total')
                            ->numeric()
                            ->prefix('R$')
                            ->readOnly()
                            ->label('Valor Total'),
                    ])
                    ->columns(3),

                Section::make('Produtos do pedido')
                    ->schema([
                        Repeater::make('itens')
                            ->relationship('itens')
                            ->schema([
                                Select::make('produto_id')
                                    ->relationship('produto', 'nome')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->label('Produto')
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                                        if (! $state) {
                                            return;
                                        }

                                        $produto = Produto::find($state);

                                        $set('preco_unitario', $produto?->preco_venda ?? 0);
                                        self::calcularTotal($get, $set);
                                    }),

                                TextInput::make('quantidade')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTotal($get, $set))
                                    ->columnSpan(1),

                                TextInput::make('preco_unitario')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTotal($get, $set))
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->columnSpanFull()
                            ->label('Produtos')
                            ->addActionLabel('Adicionar produto')
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTotal($get, $set)),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PedidoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosTable::configure($table);
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
            'index' => ListPedidos::route('/'),
            'create' => CreatePedido::route('/create'),
            'view' => ViewPedido::route('/{record}'),
            'edit' => EditPedido::route('/{record}/edit'),
        ];
    }

    public static function calcularTotal(Get $get, Set $set): void
    {
        $itens = $get('itens') ?? $get('../../itens') ?? [];
        $total = 0;

        foreach ($itens as $item) {
            $quantidade = (float) ($item['quantidade'] ?? 0);
            $preco = (float) ($item['preco_unitario'] ?? 0);
            
            $total += $quantidade * $preco;
        }

        $statePath = $get('itens') === null ? '../../valor_total' : 'valor_total';

        $set($statePath, number_format($total, 2, '.', ''));
    }
}
