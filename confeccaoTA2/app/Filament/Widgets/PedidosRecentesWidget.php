<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Pedidos\PedidoResource;
use App\Models\Pedido;
use App\Support\FilamentAccess;
use Filament\Widgets\Widget;

class PedidosRecentesWidget extends Widget
{
    protected static ?int $sort = 1;

    protected string $view = 'filament.widgets.pedidos-recentes-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return FilamentAccess::canAny('dashboard.visualizar');
    }

    protected function getViewData(): array
    {
        return [
            'pedidos' => Pedido::query()
                ->with(['cliente', 'itens'])
                ->withCount('itens')
                ->latest('updated_at')
                ->limit(8)
                ->get(),
        ];
    }

    public function getPedidoUrl(Pedido $pedido): string
    {
        if (PedidoResource::canEdit($pedido)) {
            return PedidoResource::getUrl('edit', ['record' => $pedido]);
        }

        return PedidoResource::getUrl('view', ['record' => $pedido]);
    }

    public function getPedidoActionLabel(Pedido $pedido): string
    {
        return PedidoResource::canEdit($pedido) ? 'Editar pedido' : 'Visualizar pedido';
    }

    public function statusBadgeClasses(?string $status): string
    {
        return match ($status) {
            Pedido::STATUS_FINALIZADO => 'pedido-status-badge--finalizado border-green-200 bg-green-50 text-green-700 dark:border-green-800 dark:bg-green-500/10 dark:text-green-300',
            Pedido::STATUS_EM_PRODUCAO => 'pedido-status-badge--producao border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-800 dark:bg-violet-500/10 dark:text-violet-300',
            Pedido::STATUS_PENDENTE => 'pedido-status-badge--pendente border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-800 dark:bg-amber-500/10 dark:text-amber-300',
            Pedido::STATUS_CANCELADO => 'pedido-status-badge--cancelado border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-500/10 dark:text-red-300',
            default => 'pedido-status-badge--default border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300',
        };
    }
}
