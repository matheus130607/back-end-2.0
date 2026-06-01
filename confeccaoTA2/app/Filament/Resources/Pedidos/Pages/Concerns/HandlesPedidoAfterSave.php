<?php

namespace App\Filament\Resources\Pedidos\Pages\Concerns;

use App\Models\EmailPedido;
use App\Models\Pedido;
use App\Services\PedidoEmailService;
use Filament\Notifications\Notification;

trait HandlesPedidoAfterSave
{
    protected function finalizarFluxoDoPedido(Pedido $pedido): void
    {
        $pedido->loadMissing(['itens.produto', 'cliente']);
        $pedido->recalcularValorTotal();

        $registro = app(PedidoEmailService::class)->enviarFinalizacao($pedido->refresh());

        $this->notificarStatusDoEmail($registro);
    }

    protected function notificarStatusDoEmail(?EmailPedido $registro): void
    {
        if (! $registro) {
            return;
        }

        match ($registro->status_envio) {
            'enviado' => Notification::make()
                ->success()
                ->title('E-mail de finalização enviado')
                ->body('O cliente recebeu o resumo do pedido.')
                ->send(),
            'sem_email' => Notification::make()
                ->warning()
                ->title('Cliente sem e-mail cadastrado')
                ->body('O pedido foi salvo, mas o envio foi apenas registrado na Caixa de E-mail.')
                ->send(),
            'erro' => Notification::make()
                ->danger()
                ->title('Falha ao enviar e-mail')
                ->body('O erro foi registrado na Caixa de E-mail para conferência.')
                ->send(),
            default => null,
        };
    }
}
