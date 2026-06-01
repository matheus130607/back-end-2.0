<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use App\Support\FilamentAccess;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PedidosOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected ?string $heading = 'Resumo de vendas';

    public static function canView(): bool
    {
        return FilamentAccess::canAny('dashboard.visualizar');
    }

    protected function getStats(): array
    {
        $totalPedidos = Pedido::count();
        $finalizados = Pedido::where('status', Pedido::STATUS_FINALIZADO)->count();
        $emProducao = Pedido::where('status', Pedido::STATUS_EM_PRODUCAO)->count();
        $pendentes = Pedido::where('status', Pedido::STATUS_PENDENTE)->count();
        $valorVendido = Pedido::where('status', Pedido::STATUS_FINALIZADO)->sum('valor_total');

        return [
            Stat::make('Total de pedidos', $totalPedidos)
                ->icon(Heroicon::OutlinedShoppingBag)
                ->color('primary'),

            Stat::make('Finalizados', $finalizados)
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),

            Stat::make('Em produção', $emProducao)
                ->icon(Heroicon::OutlinedClock)
                ->color('info'),

            Stat::make('Pendentes', $pendentes)
                ->icon(Heroicon::OutlinedExclamationCircle)
                ->color('warning'),

            Stat::make('Valor total vendido', 'R$ ' . number_format((float) $valorVendido, 2, ',', '.'))
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->color('success'),
        ];
    }
}
