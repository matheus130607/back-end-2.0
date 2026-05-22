@php
    $description = $description ?? 'Este é um e-mail de teste gerado para validar o envio de notificações do sistema SAFE no ambiente local com Mailpit.';
    $isSaida = ($movimentacao['tipo_chave'] ?? 'saida') === 'saida';
    $responsavelLabel = $movimentacao['responsavel_label'] ?? 'Responsável pela validação';
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SAFE - Notificação de movimentação escolar</title>
</head>
<body style="margin: 0; padding: 0; background: #f1f5f9; color: #0f172a; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width: 100%; background: #f1f5f9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width: 100%; max-width: 640px; overflow: hidden; border-radius: 14px; background: #ffffff; border: 1px solid #dbe3ef;">
                    <tr>
                        <td style="background: #1d4ed8; padding: 28px 32px;">
                            <div style="font-size: 32px; line-height: 1; font-weight: 800; letter-spacing: 0; color: #ffffff;">SAFE</div>
                            <div style="margin-top: 8px; font-size: 14px; line-height: 1.5; color: #dbeafe;">Sistema de Autorização e Fluxo Escolar</div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 32px 10px;">
                            <h1 style="margin: 0; font-size: 22px; line-height: 1.3; color: #0f172a;">Notificação de movimentação escolar</h1>
                            <p style="margin: 12px 0 0; font-size: 15px; line-height: 1.7; color: #334155;">
                                {{ $description }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width: 100%; border: 1px solid #dbe3ef; border-radius: 12px; background: #f8fafc;">
                                <tr>
                                    <td style="padding: 22px;">
                                        <div style="display: inline-block; padding: 7px 12px; border-radius: 999px; background: #dcfce7; color: #166534; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
                                            {{ $movimentacao['status'] }}
                                        </div>

                                        <p style="margin: 18px 0 22px; font-size: 16px; line-height: 1.7; color: #020617;">
                                            @if ($isSaida)
                                                Confirmamos que o aluno <strong>{{ $movimentacao['aluno'] }}</strong>, da turma <strong>{{ $movimentacao['turma'] }}</strong>, teve sua saída antecipada registrada e validada pela portaria às <strong>{{ $movimentacao['horario'] }}</strong>.
                                            @else
                                                Informamos que o aluno <strong>{{ $movimentacao['aluno'] }}</strong>, da turma <strong>{{ $movimentacao['turma'] }}</strong>, teve sua entrada tardia registrada pela Secretaria às <strong>{{ $movimentacao['horario'] }}</strong>.
                                            @endif
                                        </p>

                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #475569; font-size: 13px;">Aluno</td>
                                                <td align="right" style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #020617; font-size: 13px; font-weight: 700;">{{ $movimentacao['aluno'] }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #475569; font-size: 13px;">Turma</td>
                                                <td align="right" style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #020617; font-size: 13px; font-weight: 700;">{{ $movimentacao['turma'] }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #475569; font-size: 13px;">Tipo de movimentação</td>
                                                <td align="right" style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #020617; font-size: 13px; font-weight: 700;">{{ $movimentacao['tipo'] }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #475569; font-size: 13px;">Horário registrado</td>
                                                <td align="right" style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #020617; font-size: 13px; font-weight: 700;">{{ $movimentacao['horario'] }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #475569; font-size: 13px;">{{ $responsavelLabel }}</td>
                                                <td align="right" style="padding: 10px 0; border-top: 1px solid #e2e8f0; color: #020617; font-size: 13px; font-weight: 700;">{{ $movimentacao['responsavel_validacao'] }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 8px 32px 30px;">
                            <p style="margin: 0; font-size: 13px; line-height: 1.7; color: #475569;">
                                Esta mensagem foi gerada automaticamente pelo sistema SAFE. Não responda este e-mail.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
