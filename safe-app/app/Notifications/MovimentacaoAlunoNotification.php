<?php

namespace App\Notifications;

use App\Models\Authorization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class MovimentacaoAlunoNotification extends Notification
{
    use Queueable;

    protected $authorization;

    public function __construct(Authorization $authorization)
    {
        $this->authorization = $authorization;
    }

    // Define que usaremos o canal de e-mail padrão do Laravel
    public function via($notifiable)
    {
        return ['mail'];
    }

    // Configuração do E-mail Real -> Vai cair direto no seu Mailpit
    public function toMail($notifiable)
    {
        $aluno = $this->authorization->student->name;
        $tipo = strtoupper($this->authorization->type);

        return (new MailMessage)
            ->subject("SAFE Alerta: Movimentação Escolar - Envio Concluído")
            ->greeting("Prezado(a) Responsável,")
            ->line("Informamos que o processo de **{$tipo}** do aluno **{$aluno}** foi validado na portaria da escola.")
            ->line("Horário exato da passagem: " . $this->authorization->validated_at->format('H:i:s'))
            ->line("Seu dependente já se encontra sob os cuidados da equipe indicada.")
            ->action('Visualizar no SAFE', url('/'));
    }

    // Resolução do Desafio: Simulação do WhatsApp usando Log Nativo do Laravel
    public function simularWhatsapp($telefoneResponsavel)
    {
        $aluno = $this->authorization->student->name;
        $tipo = strtoupper($this->authorization->type);
        $horario = $this->authorization->validated_at->format('H:i');

        Log::info("----------------------------------------------------------------");
        Log::info("[DISPARO WHATSAPP SIMULADO] -> Enviando para: " . $telefoneResponsavel);
        Log::info("Mensagem: *Notificação SAFE:* Olá! Confirmamos que {$aluno} realizou a {$tipo} na portaria às {$horario}.");
        Log::info("----------------------------------------------------------------");
    }
}