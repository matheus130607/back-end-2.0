<?php

use App\Http\Controllers\AuthorizationController;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Route;

// Rota base apenas para checar se o sistema está online
Route::get('/', function () {
    return response()->json(['status' => 'SAFE Online']);
});

// 1. Rota de Massa de Dados (Apenas para criar os usuários de teste no banco)
Route::get('/seed-teste', function() {
    // Evita duplicar se você rodar mais de uma vez
    if (User::where('email', 'pai@email.com')->exists()) {
        return response()->json(['message' => 'Os dados de teste já existem no banco!']);
    }

    // Criando o Autorizador (Pai)
    $autorizador = User::create([
        'name' => 'Marcos Rocha (Pai)',
        'email' => 'pai@email.com',
        'password' => bcrypt('123456'),
        'role' => 'autorizador',
        'phone' => '11999998888'
    ]);

    // Criando o Professor
    User::create([
        'name' => 'Profª Ana Souza',
        'email' => 'ana@escola.com',
        'password' => bcrypt('123456'),
        'role' => 'professor'
    ]);

    // Criando o Aluno vinculado ao Pai
    $aluno = Student::create([
        'name' => 'Guilherme Rocha',
        'classroom' => '4º Ano B',
        'autorizador_id' => $autorizador->id
    ]);

    return response()->json([
        'message' => 'Massa de dados criada com sucesso para testes!',
        'autorizador_id' => $autorizador->id,
        'student_id' => $aluno->id
    ]);
});

// 2. Rota que simula o clique do Autorizador no aplicativo enviando os dados
Route::post('/autorizador/solicitar', [AuthorizationController::class, 'solicitarFluxo']);