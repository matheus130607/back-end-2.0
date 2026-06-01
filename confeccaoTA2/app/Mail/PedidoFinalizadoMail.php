<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PedidoFinalizadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Pedido $pedido)
    {
        $this->pedido->loadMissing(['cliente', 'itens.produto']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pedido #{$this->pedido->id} finalizado com sucesso",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pedidos.finalizado',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
