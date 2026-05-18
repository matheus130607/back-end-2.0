<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use App\Models\Student;
use Illuminate\Http\Request;

class AuthorizationController extends Controller
{
    /**
     * O Autorizador solicita e o sistema já gera como APROVADO (status: autorizado).
     */
    public function solicitarFluxo(Request $request)
    {
        // 1. Validação dos dados recebidos
        $request->validate([
            'student_id'     => 'required|exists:students,id',
            'autorizador_id' => 'required|exists:users,id', // Simulando o ID do Pai logado por enquanto
            'type'           => 'required|in:entrada,saida',
        ]);

        // 2. Segurança: Verificar se o aluno realmente pertence a este autorizador
        $student = Student::find($request->student_id);
        
        if ($student->autorizador_id != $request->autorizador_id) {
            return response()->json([
                'error' => 'Ação não autorizada. Este aluno não está vinculado a este responsável.'
            ], 403);
        }

        // 3. Verificar se já não existe um fluxo idêntico 'autorizado' aberto hoje para evitar duplicidade
        $fluxoExistente = Authorization::where('student_id', $request->student_id)
            ->where('type', $request->type)
            ->where('status', 'autorizado')
            ->whereDate('created_at', now()->today())
            ->first();

        if ($fluxoExistente) {
            return response()->json([
                'error' => "Já existe uma solicitação de {$request->type} ativa para este aluno hoje."
            ], 400);
        }

        // 4. Criação do Fluxo - Note que o status padrão já é 'autorizado' no banco de dados
        $authorization = Authorization::create([
            'student_id'     => $request->student_id,
            'autorizador_id' => $request->autorizador_id,
            'type'           => $request->type,
            'status'         => 'autorizado', // Garante a aprovação imediata
        ]);

        // Mensagem customizada baseada no tipo de fluxo (sua regra de negócio)
        $mensagem = $request->type === 'saida' 
            ? 'Solicitação de SAÍDA realizada. Professor e Portaria já foram notificados.'
            : 'Solicitação de ENTRADA realizada. O Aluno está a caminho da escola.';

        return response()->json([
            'message' => $mensagem,
            'dados'   => $authorization->load('student')
        ], 201);
    }

    public function painelProfessor(Request $request)
    {
    // O professor filtra pela turma que ele está lecionando no momento
    $request->validate([
        'classroom' => 'required|string' // Ex: "4º Ano B"
    ]);

    // Busca as autorizações de hoje para a turma informada
    $fluxosDoDia = Authorization::with('student')
        ->whereHas('student', function ($query) use ($request) {
            $query->where('classroom', $request->classroom);
        })
        ->whereDate('created_at', now()->today())
        ->get();

    return response()->json([
        'turma' => $request->classroom,
        'alertas_para_o_professor' => $fluxosDoDia
    ]);
}
}