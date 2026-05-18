<?php
use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\PortariaController;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Route;

// Rota base
Route::get('/', function () {
    return response()->json(['status' => 'SAFE Online']);
});

// 1. Criar Massa de Dados
Route::get('/seed-teste', function() {
    if (User::where('email', 'pai@email.com')->exists()) {
        return response()->json(['message' => 'Os dados de teste já existem no banco!']);
    }

    $autorizador = User::create([
        'name' => 'Marcos Rocha (Pai)',
        'email' => 'pai@email.com',
        'password' => bcrypt('123456'),
        'role' => 'autorizador',
        'phone' => '11999998888'
    ]);

    User::create([
        'name' => 'Profª Ana Souza',
        'email' => 'ana@escola.com',
        'password' => bcrypt('123456'),
        'role' => 'professor'
    ]);

    // Criando o Porteiro Silva para a portaria usar mais tarde
    User::create([
        'name' => 'Porteiro Silva',
        'email' => 'silva@escola.com',
        'password' => bcrypt('123456'),
        'role' => 'portaria'
    ]);

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

// 2. ROTA DE TESTE DA SECRETARIA (Modificada para GET para funcionar no navegador)
Route::get('/teste/secretaria/solicitar', function() {
    // Simula o preenchimento de um formulário da secretaria
    $dadosSimulados = new \Illuminate\Http\Request([
        'student_id'     => 1,
        'autorizador_id' => 1,
        'type'           => 'saida' // Pode mudar para 'entrada' se quiser testar o outro fluxo
    ]);

    return app(AuthorizationController::class)->solicitarFluxo($dadosSimulados);
});

// 3. Painel do Professor
Route::get('/professor/painel', [AuthorizationController::class, 'painelProfessor']);

// 4. ROTA DE TESTE DA PORTARIA (Modificada para GET para funcionar no navegador)
Route::get('/teste/portaria/liberar/{id}', [PortariaController::class, 'liberarAluno']);