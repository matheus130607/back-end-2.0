<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FornecedorController extends Controller
{
   public function index() {
        try {
            $fornecedors = \App\Models\Fornecedor::all(); // Busca todos os itens do estoque
        } catch (\Exception $e) {
            // se houver problema com o banco (ex: driver ausente), exibir dados estáticos
            $fornecedors = collect([
                (object)[
                    'nome' => 'Fornecedores Exemplo',
                    'email' => 'fornecedor@gmail.com',
                    'cnpj' => '123.123.123/0001-12',
                ],
            ]);
        }

        // A view deve ficar em resources/views/fornecedor/index.blade.php
        // o controller anterior estava com digitação errada (forncedor).
        return view('fornecedor.index', compact('fornecedors'));
    }
}
