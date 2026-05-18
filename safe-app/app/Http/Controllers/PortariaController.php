<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use App\Notifications\MovimentacaoAlunoNotification;
use Illuminate\Http\Request;

class PortariaController extends Controller
{
    public function liberarAluno(Request $request, $id)
    {
        // 1. Busca a autorização criada pela secretaria
        $authorization = Authorization::with('student.autorizador')->findOrFail($id);

        if ($authorization->status !== 'autorizado') {
            return response()->json(['error' => 'Este fluxo já foi encerrado ou cancelado.'], 400);
        }

        // 2. Portaria carimba a saída/entrada física do aluno no exato momento
        $authorization->update([
            'status' => 'realizado',
            'portaria_id' => 3, // ID simulado do Porteiro Silva que criamos no seed
            'validated_at' => now()
        ]);

        // 3. Busca o usuário responsável (o pai/mãe vinculado ao aluno) para notificar
        $responsavel = $authorization->student->autorizador; 

        // 4. Disparos do Desafio
        // Envia o e-mail real para o Mailpit
        $notification = new MovimentacaoAlunoNotification($authorization);
        $responsavel->notify($notification);

        // Executa a simulação do WhatsApp no arquivo de Log
        $notification->simularWhatsapp($responsavel->phone);

        return response()->json([
            'message' => 'Portaria liberada! Aluno liberado e responsáveis notificados com sucesso.',
            'dados' => $authorization
        ]);
    }
}