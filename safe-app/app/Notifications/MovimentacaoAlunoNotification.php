<?php

namespace App\Notifications;

use App\Models\Authorization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class MovimentacaoAlunoNotification extends Notification
{
    use Queueable;

    public function __construct(protected Authorization $authorization)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $authorization = $this->authorization->loadMissing(['student.classroomGroup', 'responsible']);
        $student = $authorization->student;
        $classroom = $student->classroomName();
        $status = $authorization->statusLabel();

        if ($authorization->isSaida()) {
            $hour = $authorization->validated_at?->format('H:i') ?? now()->format('H:i');

            return (new MailMessage)
                ->subject('SAFE - Saida antecipada realizada')
                ->greeting('Ola, ' . $notifiable->name)
                ->line("Confirmamos que {$student->name}, da turma {$classroom}, realizou saida antecipada.")
                ->line("Horario exato da saida: {$hour}.")
                ->line("Status do registro: {$status}.")
                ->action('Acessar SAFE', url('/dashboard'));
        }

        $hour = $authorization->requested_at?->format('H:i') ?? now()->format('H:i');

        return (new MailMessage)
            ->subject('SAFE - Entrada tardia registrada')
            ->greeting('Ola, ' . $notifiable->name)
            ->line("Informamos que {$student->name}, da turma {$classroom}, teve entrada tardia registrada.")
            ->line("Horario informado pela Secretaria: {$hour}.")
            ->line("Status do registro: {$status}.")
            ->action('Acessar SAFE', url('/dashboard'));
    }

    public function simularWhatsapp(?string $telefoneResponsavel = null): void
    {
        $authorization = $this->authorization->loadMissing(['student.classroomGroup', 'responsible']);
        $responsible = $authorization->responsible;
        $student = $authorization->student;
        $classroom = $student->classroomName();
        $type = $authorization->isSaida() ? 'SAIDA ANTECIPADA' : 'ENTRADA TARDIA';
        $hour = $authorization->isSaida()
            ? ($authorization->validated_at?->format('H:i') ?? now()->format('H:i'))
            : ($authorization->requested_at?->format('H:i') ?? now()->format('H:i'));
        $phone = $telefoneResponsavel ?: ($responsible->phone ?? 'telefone nao informado');
        $message = $authorization->isSaida()
            ? "Confirmamos que o aluno saiu da escola as {$hour}."
            : "Informamos que a entrada tardia do aluno foi registrada as {$hour}.";

        Log::info("[SAFE][WHATSAPP_SIMULADO]\n"
            . 'Responsavel: ' . ($responsible->name ?? 'Nao informado') . "\n"
            . 'Telefone: ' . $phone . "\n"
            . 'Aluno: ' . $student->name . "\n"
            . 'Turma: ' . $classroom . "\n"
            . 'Tipo: ' . $type . "\n"
            . 'Horario: ' . $hour . "\n"
            . 'Mensagem: ' . $message);
    }
}
