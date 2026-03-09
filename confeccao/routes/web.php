<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\ProdutoController;

Route::get('/', function () {
    return view('welcome');
});

// As páginas de clientes e pedidos devem ser vistas apenas por usuários
// autenticados. Sem login, o layout tenta acessar Auth::user()->name e acaba
// lançando "Attempt to read property 'name' on null".
Route::middleware('auth')->group(function () {
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedido.index');
    Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index');
    Route::get('/fornecedor', [FornecedorController::class, 'index'])->name('fornecedor.index');
    Route::get('/produto', [ProdutoController::class, 'index'])->name('produto.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rota para mostrar o formulário
Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
Route::get('/pedidos/create', [PedidoController::class, 'create'])->name('pedido.create');
Route::get('/estoque/create', [EstoqueController::class, 'create'])->name('estoque.create');
Route::get('/fornecedor/create', [FornecedorController::class, 'create'])->name('fornecedor.create');
Route::get('/produto/create', [ProdutoController::class, 'create'])->name('produtos.create');


//Rota para RECEBER os dados e salvar (POST)
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedido.store');
Route::post('/estoque', [EstoqueController::class, 'store'])->name('estoque.store');
Route::post('/fornecedor', [FornecedorController::class, 'store'])->name('fornecedor.store');
Route::post('/produto', [ProdutoController::class, 'store'])->name('produtos.store');


require __DIR__.'/auth.php';
