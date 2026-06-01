<?php

namespace App\Services;

use App\Mail\PedidoFinalizadoMail;
use App\Models\EmailPedido;
use App\Models\ItemPedido;
use App\Models\Pedido;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class PedidoEmailService
{
    public function enviarFinalizacao(Pedido $pedido): ?EmailPedido
    {
        $pedido->loadMissing(['cliente', 'itens.produto']);

        if (! $pedido->isFinalizado() || $pedido->email_enviado_em) {
            return null;
        }

        if ($pedido->emailsPedidos()->whereIn('status_envio', ['enviado', 'sem_email'])->exists()) {
            return null;
        }

        $cliente = $pedido->cliente;
        $assunto = "Pedido #{$pedido->id} finalizado com sucesso";

        if (blank($cliente?->email)) {
            return EmailPedido::create([
                'pedido_id' => $pedido->id,
                'cliente_id' => $cliente?->id,
                'email_destinatario' => null,
                'assunto' => $assunto,
                'status_envio' => 'sem_email',
                'conteudo_resumo' => $this->montarResumo($pedido),
                'erro_envio' => 'Cliente sem e-mail cadastrado.',
            ]);
        }

        $registro = EmailPedido::create([
            'pedido_id' => $pedido->id,
            'cliente_id' => $cliente->id,
            'email_destinatario' => $cliente->email,
            'assunto' => $assunto,
            'status_envio' => 'pendente',
            'conteudo_resumo' => $this->montarResumo($pedido),
        ]);

        try {
            Mail::to($cliente->email)->send(new PedidoFinalizadoMail($pedido));

            $enviadoEm = now();

            $registro->update([
                'status_envio' => 'enviado',
                'enviado_em' => $enviadoEm,
                'erro_envio' => null,
            ]);

            $pedido->forceFill([
                'email_enviado_em' => $enviadoEm,
            ])->saveQuietly();
        } catch (Throwable $exception) {
            $registro->update([
                'status_envio' => 'erro',
                'erro_envio' => $exception->getMessage(),
            ]);

            Log::warning('Falha ao enviar e-mail de pedido finalizado.', [
                'pedido_id' => $pedido->id,
                'cliente_id' => $cliente->id,
                'erro' => $exception->getMessage(),
            ]);
        }

        return $registro->refresh();
    }

    private function montarResumo(Pedido $pedido): string
    {
        $linhas = $pedido->itens
            ->map(function (ItemPedido $item): string {
                $produto = $item->produto?->nome ?? 'Produto removido';
                $subtotal = $item->subtotal;

                return sprintf(
                    '%s - Qtd: %s - Unitário: R$ %s - Subtotal: R$ %s',
                    $produto,
                    $item->quantidade,
                    number_format((float) $item->preco_unitario, 2, ',', '.'),
                    number_format($subtotal, 2, ',', '.'),
                );
            })
            ->implode(PHP_EOL);

        return trim(sprintf(
            "Cliente: %s\nPedido: #%s\nStatus: %s\nTotal: R$ %s\n\nProdutos:\n%s",
            $pedido->cliente?->nome ?? 'Cliente removido',
            $pedido->id,
            $pedido->status,
            number_format((float) $pedido->valor_total, 2, ',', '.'),
            $linhas ?: 'Nenhum produto informado.',
        ));
    }
}
