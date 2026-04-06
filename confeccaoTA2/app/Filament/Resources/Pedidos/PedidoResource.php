<?php

namespace App\Filament\Resources\Pedidos;


use App\Filament\Resources\Pedidos\Pages\CreatePedido;
use App\Filament\Resources\Pedidos\Pages\EditPedido;
use App\Filament\Resources\Pedidos\Pages\ListPedidos;
use App\Filament\Resources\Pedidos\Pages\ViewPedido;
use App\Filament\Resources\Pedidos\Schemas\PedidoForm;
use App\Filament\Resources\Pedidos\Schemas\PedidoInfolist;
use App\Filament\Resources\Pedidos\Tables\PedidosTable;
use App\Models\Pedido;
use App\Models\Produto;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 1;

    // Nome que vai aparecer no menu Integral
    protected static ?string $navigationLabel = 'Pedido';

    // Nome singular (ex: usado no botão "Criar Usuário")
    protected static ?string $modelLabel = 'Pedido';

    // Nome plural (ex: usado no título da tabela "Usuários")
    protected static ?string $pluralModelLabel = 'Pedidos';

    protected static ?string $recordTitleAttribute = 'Pedido';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([

                Select::make('cliente_id')
                    ->relationship('cliente', 'nome')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Selecione o Cliente'),

                Select::make('status')
                    ->options([
                        'Pendente' => 'Pendente',
                        'Em Produção' => 'Em Produção',
                        'Finalizado' => 'Finalizado',
                    ])
                    ->default('Pendente')
                    ->required(),

                TextInput::make('valor_total')
                    ->numeric()
                    ->prefix('R$')
                    ->readOnly() 
                    ->label('Valor Total'),

                Repeater::make('itens')
                    ->relationship('itens') 
                    ->schema([

                Select::make('produto_id')
                    ->relationship('produto', 'nome')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Produto')
                    ->columnSpan(2)
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            $produto = Produto::find($state);
                            $set('preco_unitario', $produto->preco_venda ?? 0);
                        }
                    }),

                TextInput::make('quantidade')
                    ->numeric()
                    ->default(1)
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
                    ->label('Produtos do Pedido')
                    ->live()

                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTotal($get, $set)),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PedidoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosTable::configure($table);
        return $table
            ->columns([

                TextColumn::make('cliente.nome')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge() 
                    ->color(fn (string $state): string => match ($state) {
                        'Pendente' => 'warning',
                        'Em Produção' => 'info',
                        'Finalizado' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('valor_total')
                    ->label('Valor Total')
                    ->money('BRL') 
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Data do Pedido')
                    ->dateTime('d/m/Y H:i') 
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => ListPedidos::route('/'),
            'create' => CreatePedido::route('/create'),
            'view' => ViewPedido::route('/{record}'),
            'edit' => EditPedido::route('/{record}/edit'),
        ];
    }

    public static function calcularTotal(Get $get, Set $set): void {

        // Pega todos os itens que estão no Repeater naquele momento
        $itens = $get('itens') ?? [];
        $total = 0;

        //Passa por cada linha somando (quantidade * preco)
        foreach ($itens as $item) {
            $quantidade = (float) ($item['quantidade'] ?? 0);
            $preco = (float) ($item['preco_unitario'] ?? 0);
            
            $total += $quantidade * $preco;
        }

        // Joga o resultado de volta lá no campo 'valor_total'
        $set('valor_total', number_format($total, 2, '.', ''));
    }
}
