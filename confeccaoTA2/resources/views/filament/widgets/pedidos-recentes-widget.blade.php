<x-filament-widgets::widget>
    @once
        <style>
            .pedidos-recentes-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr));
                gap: 1rem;
            }

            .pedidos-recentes-card {
                display: flex;
                min-width: 0;
                height: 100%;
                flex-direction: column;
                border: 1px solid var(--gray-200, #e5e7eb);
                border-radius: 0.75rem;
                background: #ffffff;
                padding: 1rem;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
            }

            .pedidos-recentes-card:where(.dark, .dark *) {
                border-color: var(--gray-700, #374151);
                background: var(--gray-900, #111827);
            }

            .pedidos-recentes-card__header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 0.75rem;
            }

            .pedidos-recentes-card__title {
                margin: 0;
                color: var(--gray-950, #030712);
                font-size: 1rem;
                font-weight: 650;
                line-height: 1.5rem;
            }

            .pedidos-recentes-card__title:where(.dark, .dark *) {
                color: #ffffff;
            }

            .pedidos-recentes-card__client {
                margin-top: 0.25rem;
                overflow: hidden;
                color: var(--gray-500, #6b7280);
                font-size: 0.875rem;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .pedidos-recentes-card__client:where(.dark, .dark *) {
                color: var(--gray-400, #9ca3af);
            }

            .pedidos-recentes-metrics {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(min(100%, 132px), 1fr));
                gap: 0.75rem;
                margin-top: 1rem;
            }

            .pedidos-recentes-metric {
                border: 1px solid var(--gray-200, #e5e7eb);
                border-radius: 0.5rem;
                background: var(--gray-50, #f9fafb);
                padding: 0.625rem 0.75rem;
            }

            .pedidos-recentes-metric:where(.dark, .dark *) {
                border-color: var(--gray-700, #374151);
                background: rgba(31, 41, 55, 0.7);
            }

            .pedidos-recentes-metric__label {
                color: var(--gray-500, #6b7280);
                font-size: 0.75rem;
                font-weight: 500;
                line-height: 1rem;
            }

            .pedidos-recentes-metric__label:where(.dark, .dark *) {
                color: var(--gray-400, #9ca3af);
            }

            .pedidos-recentes-metric__value {
                margin: 0.25rem 0 0;
                color: var(--gray-950, #030712);
                font-size: 0.875rem;
                font-weight: 650;
                line-height: 1.25rem;
            }

            .pedidos-recentes-metric__value:where(.dark, .dark *) {
                color: #ffffff;
            }

            .pedidos-recentes-card__footer {
                display: flex;
                margin-top: auto;
                padding-top: 1rem;
            }

            .pedidos-recentes-card__divider {
                border-top: 1px solid var(--gray-200, #e5e7eb);
            }

            .pedidos-recentes-card__divider:where(.dark, .dark *) {
                border-color: var(--gray-700, #374151);
            }

            .pedido-status-badge {
                display: inline-flex;
                flex-shrink: 0;
                align-items: center;
                border: 1px solid;
                border-radius: 999px;
                padding: 0.25rem 0.625rem;
                font-size: 0.75rem;
                font-weight: 650;
                line-height: 1rem;
                white-space: nowrap;
            }

            .pedido-status-badge--finalizado {
                border-color: #bbf7d0;
                background: #dcfce7;
                color: #166534;
            }

            .pedido-status-badge--finalizado:where(.dark, .dark *) {
                border-color: #166534;
                background: rgba(34, 197, 94, 0.12);
                color: #86efac;
            }

            .pedido-status-badge--producao {
                border-color: #ddd6fe;
                background: #ede9fe;
                color: #5b21b6;
            }

            .pedido-status-badge--producao:where(.dark, .dark *) {
                border-color: #5b21b6;
                background: rgba(139, 92, 246, 0.12);
                color: #c4b5fd;
            }

            .pedido-status-badge--pendente {
                border-color: #fde68a;
                background: #fef9c3;
                color: #854d0e;
            }

            .pedido-status-badge--pendente:where(.dark, .dark *) {
                border-color: #92400e;
                background: rgba(245, 158, 11, 0.12);
                color: #fcd34d;
            }

            .pedido-status-badge--cancelado {
                border-color: #fecaca;
                background: #fee2e2;
                color: #991b1b;
            }

            .pedido-status-badge--cancelado:where(.dark, .dark *) {
                border-color: #991b1b;
                background: rgba(239, 68, 68, 0.12);
                color: #fca5a5;
            }

            .pedido-status-badge--default {
                border-color: var(--gray-200, #e5e7eb);
                background: var(--gray-50, #f9fafb);
                color: var(--gray-700, #374151);
            }

            .pedido-status-badge--default:where(.dark, .dark *) {
                border-color: var(--gray-700, #374151);
                background: var(--gray-800, #1f2937);
                color: var(--gray-300, #d1d5db);
            }

            .pedidos-recentes-empty {
                grid-column: 1 / -1;
                border: 1px dashed var(--gray-300, #d1d5db);
                border-radius: 0.75rem;
                padding: 1.5rem;
                color: var(--gray-500, #6b7280);
                font-size: 0.875rem;
            }

            .pedidos-recentes-empty:where(.dark, .dark *) {
                border-color: var(--gray-700, #374151);
                color: var(--gray-400, #9ca3af);
            }
        </style>
    @endonce

    <x-filament::section>
        <x-slot name="heading">
            Últimos pedidos
        </x-slot>

        <div class="pedidos-recentes-grid grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @forelse ($pedidos as $pedido)
                <article class="pedidos-recentes-card flex h-full flex-col rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition dark:border-gray-700 dark:bg-gray-900">
                    <div class="pedidos-recentes-card__header flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="pedidos-recentes-card__title text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                Pedido #{{ $pedido->id }}
                            </h3>
                            <p class="pedidos-recentes-card__client mt-1 truncate text-sm text-gray-500 dark:text-gray-400">
                                Cliente: {{ $pedido->cliente?->nome ?? 'Cliente removido' }}
                            </p>
                        </div>

                        <span class="pedido-status-badge {{ $this->statusBadgeClasses($pedido->status) }} inline-flex shrink-0 items-center rounded-full border px-2.5 py-1 text-xs font-semibold">
                            {{ $pedido->status }}
                        </span>
                    </div>

                    <dl class="pedidos-recentes-metrics mt-4 grid gap-3 sm:grid-cols-3 md:grid-cols-1 xl:grid-cols-1 2xl:grid-cols-3">
                        <div class="pedidos-recentes-metric rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-800/70">
                            <dt class="pedidos-recentes-metric__label text-xs font-medium text-gray-500 dark:text-gray-400">Valor</dt>
                            <dd class="pedidos-recentes-metric__value mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                                R$ {{ number_format((float) $pedido->valor_total, 2, ',', '.') }}
                            </dd>
                        </div>
                        <div class="pedidos-recentes-metric rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-800/70">
                            <dt class="pedidos-recentes-metric__label text-xs font-medium text-gray-500 dark:text-gray-400">Produtos</dt>
                            <dd class="pedidos-recentes-metric__value mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                                {{ $pedido->itens_count }} {{ $pedido->itens_count === 1 ? 'item' : 'itens' }}
                            </dd>
                        </div>
                        <div class="pedidos-recentes-metric rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-800/70">
                            <dt class="pedidos-recentes-metric__label text-xs font-medium text-gray-500 dark:text-gray-400">Atualizado em</dt>
                            <dd class="pedidos-recentes-metric__value mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                                {{ $pedido->updated_at?->format('d/m/Y') }}
                            </dd>
                        </div>
                    </dl>

                    <div class="pedidos-recentes-card__footer pedidos-recentes-card__divider mt-4 flex border-t border-gray-200 pt-4 dark:border-gray-700">
                        <x-filament::button
                            tag="a"
                            :href="$this->getPedidoUrl($pedido)"
                            icon="heroicon-m-pencil-square"
                            size="sm"
                        >
                            {{ $this->getPedidoActionLabel($pedido) }}
                        </x-filament::button>
                    </div>
                </article>
            @empty
                <div class="pedidos-recentes-empty col-span-full rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    Nenhum pedido cadastrado ainda.
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
