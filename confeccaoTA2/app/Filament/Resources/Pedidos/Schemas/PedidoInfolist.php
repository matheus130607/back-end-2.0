<?php

namespace App\Filament\Resources\Pedidos\Schemas;

use App\Models\Pedido;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PedidoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Resumo do pedido')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Pedido')
                            ->formatStateUsing(fn (int $state): string => "#{$state}"),
                        TextEntry::make('cliente.nome')
                            ->label('Cliente')
                            ->placeholder('Cliente removido'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (?string $state): string => Pedido::statusColor($state)),
                        TextEntry::make('valor_total')
                            ->money('BRL')
                            ->placeholder('-'),
                        TextEntry::make('email_enviado_em')
                            ->label('E-mail enviado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Não enviado'),
                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make('Produtos do pedido')
                    ->schema([
                        RepeatableEntry::make('itens')
                            ->label('')
                            ->schema([
                                TextEntry::make('produto.nome')
                                    ->label('Produto')
                                    ->placeholder('Produto removido'),
                                TextEntry::make('quantidade')
                                    ->label('Quantidade'),
                                TextEntry::make('preco_unitario')
                                    ->label('Preço unitário')
                                    ->money('BRL'),
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('BRL'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }
}
