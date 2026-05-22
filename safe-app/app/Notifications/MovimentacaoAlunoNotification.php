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

    public function __construct(protected Authorization $authorization) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->mailSubject())
            ->view('emails.teste-movimentacao-aluno', [
                'movimentacao' => $this->movimentacaoData(),
                'description' => 'Esta mensagem foi gerada para notificar o responsavel sobre uma movimentacao escolar registrada no sistema SAFE.',
            ]);
    }

    public function simularWhatsapp(?string $telefoneResponsavel = null): void
    {
        $authorization = $this->authorization->loadMissing(['student.classroomGroup', 'responsible']);
        $responsible = $authorization->responsible;
        $student = $authorization->student;
        $classroom = $student->classroomName();
        $type = $authorization->isSaida() ? 'SAIDA ANTECIPADA' : 'ENTRADA TARDIA';
        $hour = $authorization->isSaida()
            ? $this->formatHour($authorization->validated_at)
            : $this->formatHour($authorization->requested_at);
        $phone = $telefoneResponsavel ?: ($responsible->phone ?? 'telefone nao informado');
        $message = $authorization->isSaida()
            ? "Confirmamos que o aluno saiu da escola as {$hour}."
            : "Informamos que a entrada tardia do aluno foi registrada as {$hour}.";

        Log::info("[SAFE][WHATSAPP_SIMULADO]\n"
            .'Responsavel: '.($responsible->name ?? 'Nao informado')."\n"
            .'Telefone: '.$phone."\n"
            .'Aluno: '.$student->name."\n"
            .'Turma: '.$classroom."\n"
            .'Tipo: '.$type."\n"
            .'Horario: '.$hour."\n"
            .'Mensagem: '.$message);
    }

    private function formatHour($date = null): string
    {
        return ($date ? $date->copy() : now())
            ->timezone(config('app.timezone'))
            ->format('H:i');
    }

    private function mailSubject(): string
    {
        return $this->authorization->isSaida()
            ? 'SAFE - Saida antecipada realizada'
            : 'SAFE - Entrada tardia registrada';
    }

    private function movimentacaoData(): array
    {
        $authorization = $this->authorization->loadMissing([
            'student.classroomGroup',
            'responsible',
            'secretary',
            'portaria',
        ]);

        $student = $authorization->student;
        $responsibleUser = $authorization->isSaida()
            ? $authorization->portaria
            : $authorization->secretary;

        return [
            'aluno' => $student->name,
            'turma' => $student->classroomName(),
            'tipo' => $authorization->typeLabel(),
            'tipo_chave' => $authorization->type,
            'status' => $authorization->statusLabel(),
            'horario' => $authorization->isSaida()
                ? $this->formatHour($authorization->validated_at)
                : $this->formatHour($authorization->requested_at),
            'responsavel_label' => $authorization->isSaida()
                ? 'Responsavel pela validacao'
                : 'Responsavel pelo registro',
            'responsavel_validacao' => $responsibleUser?->name ?? 'Sistema SAFE',
        ];
    }
}
