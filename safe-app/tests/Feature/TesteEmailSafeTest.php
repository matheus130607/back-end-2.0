<?php

namespace Tests\Feature;

use App\Mail\TesteMovimentacaoAlunoMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TesteEmailSafeTest extends TestCase
{
    public function test_teste_email_safe_route_sends_formatted_mail(): void
    {
        Mail::fake();

        $response = $this->get('/teste/email-safe');

        $response->assertOk();
        $response->assertSee('E-mail de teste do SAFE enviado com sucesso para matheus.malaman@aluno.senai.br');

        Mail::assertSent(TesteMovimentacaoAlunoMail::class, function (TesteMovimentacaoAlunoMail $mail) {
            return $mail->hasTo('matheus.malaman@aluno.senai.br')
                && $mail->envelope()->subject === 'Teste SAFE - Notificação de Movimentação Escolar';
        });
    }
}
