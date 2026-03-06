<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index() {
        try {
            $estoques = \App\Models\Estoque::all(); // Busca todos os itens do estoque
        } catch (\Exception $e) {
            // se houver problema com o banco (ex: driver ausente), exibir dados estáticos
            $estoques = collect([
                (object)[
                    'nome' => 'Produto Exemplo',
                    'quantidade' => 0,
                ],
            ]);
        }

        // Ajuste o nome da view se estiver em outra pasta
        return view('estoque.index', compact('estoques'));
    }
}