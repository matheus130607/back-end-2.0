<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TesteMovimentacaoAlunoMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $movimentacao;

    public function __construct()
    {
        $this->movimentacao = [
            'aluno' => 'João Pedro Santos',
            'turma' => '2º DS A',
            'tipo' => 'Saída antecipada',
            'tipo_chave' => 'saida',
            'status' => 'Saída realizada',
            'horario' => now()->format('H:i'),
            'responsavel_label' => 'Responsável pela validação',
            'responsavel_validacao' => 'Portaria SAFE',
        ];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Teste SAFE - Notificação de Movimentação Escolar',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.teste-movimentacao-aluno',
            with: [
                'movimentacao' => $this->movimentacao,
            ],
        );
    }
}
