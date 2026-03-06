<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index() {
        try {
            $produtos = \App\Models\Produto::all(); // Busca todos os produtos
        } catch (\Exception $e) {
            // se houver problema com o banco (ex: driver ausente), exibir dados estáticos
            $produtos = collect([
                (object)[
                    'nome' => 'Produto Exemplo',
                    'preco' => 0.00,
                    'quantidade' => 0,
                ],
            ]);
        }

        // view onde os produtos serão exibidos
        return view('produtos.index', compact('produtos'));
    }
}