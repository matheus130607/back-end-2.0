<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortariaController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\SecretariaController;
use App\Mail\TesteMovimentacaoAlunoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/teste/email-safe', function () {
    Mail::to('matheus.malaman@aluno.senai.br')
        ->send(new TesteMovimentacaoAlunoMail());

    return 'E-mail de teste do SAFE enviado com sucesso para matheus.malaman@aluno.senai.br';
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware(['auth', 'role:secretaria'])
    ->prefix('secretaria')
    ->name('secretaria.')
    ->group(function () {
        Route::get('/dashboard', [SecretariaController::class, 'dashboard'])->name('dashboard');
        Route::post('/authorizations', [SecretariaController::class, 'storeAuthorization'])->name('authorizations.store');
    });

Route::middleware(['auth', 'role:professor'])
    ->prefix('professor')
    ->name('professor.')
    ->group(function () {
        Route::get('/dashboard', [ProfessorController::class, 'dashboard'])->name('dashboard');
        Route::post('/authorizations/{authorization}/acknowledge', [ProfessorController::class, 'acknowledge'])->name('authorizations.acknowledge');
    });

Route::middleware(['auth', 'role:portaria'])
    ->prefix('portaria')
    ->name('portaria.')
    ->group(function () {
        Route::get('/dashboard', [PortariaController::class, 'dashboard'])->name('dashboard');
        Route::post('/authorizations/{authorization}/release', [PortariaController::class, 'release'])->name('authorizations.release');
    });
