<?php

namespace Database\Seeders;

use App\Models\EmailPedido;
use App\Models\ItemPedido;
use App\Models\Pedido;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class EmailPedidoSeeder extends Seeder
{
    public function run(): void
    {
        EmailPedido::query()->delete();

        $pedidosFinalizados = Pedido::query()
            ->with(['cliente', 'itens.produto'])
            ->where('status', Pedido::STATUS_FINALIZADO)
            ->orderBy('created_at')
            ->get()
            ->values();

        $registros = [
            [
                'pedido_index' => 0,
                'status_envio' => EmailPedido::STATUS_ENVIADO,
                'enviado_em' => '2026-05-20 16:00:00',
                'erro_envio' => null,
            ],
            [
                'pedido_index' => 1,
                'status_envio' => EmailPedido::STATUS_ENVIADO,
                'enviado_em' => '2026-05-25 17:05:00',
                'erro_envio' => null,
            ],
            [
                'pedido_index' => 2,
                'status_envio' => EmailPedido::STATUS_PENDENTE,
                'enviado_em' => null,
                'erro_envio' => null,
            ],
            [
                'pedido_index' => 3,
                'status_envio' => EmailPedido::STATUS_ERRO,
                'enviado_em' => null,
                'erro_envio' => 'Falha simulada: serviço SMTP indisponível no momento do envio.',
            ],
        ];

        foreach ($registros as $registroData) {
            $pedido = $pedidosFinalizados->get($registroData['pedido_index']);

            if (! $pedido) {
                continue;
            }

            $enviadoEm = $registroData['enviado_em']
                ? CarbonImmutable::parse($registroData['enviado_em'])
                : null;

            EmailPedido::create([
                'pedido_id' => $pedido->id,
                'cliente_id' => $pedido->cliente_id,
                'email_destinatario' => $pedido->cliente->email,
                'assunto' => "Pedido #{$pedido->id} finalizado com sucesso",
                'status_envio' => $registroData['status_envio'],
                'enviado_em' => $enviadoEm,
                'conteudo_resumo' => $this->montarResumo($pedido),
                'erro_envio' => $registroData['erro_envio'],
                'created_at' => $enviadoEm ?? $pedido->updated_at,
                'updated_at' => $enviadoEm ?? $pedido->updated_at,
            ]);

            if ($registroData['status_envio'] === EmailPedido::STATUS_ENVIADO) {
                Pedido::withoutTimestamps(function () use ($pedido, $enviadoEm): void {
                    $pedido->forceFill(['email_enviado_em' => $enviadoEm])->saveQuietly();
                });
            }
        }
    }

    private function montarResumo(Pedido $pedido): string
    {
        $linhas = $pedido->itens
            ->map(function (ItemPedido $item): string {
                return sprintf(
                    '%s - Qtd: %s - Unitário: R$ %s - Subtotal: R$ %s',
                    $item->produto?->nome ?? 'Produto removido',
                    $item->quantidade,
                    number_format((float) $item->preco_unitario, 2, ',', '.'),
                    number_format($item->subtotal, 2, ',', '.'),
                );
            })
            ->implode(PHP_EOL);

        return trim(sprintf(
            "Cliente: %s\nPedido: #%s\nStatus: %s\nTotal: R$ %s\n\nProdutos:\n%s",
            $pedido->cliente?->nome ?? 'Cliente removido',
            $pedido->id,
            $pedido->status,
            number_format((float) $pedido->valor_total, 2, ',', '.'),
            $linhas,
        ));
    }
}
