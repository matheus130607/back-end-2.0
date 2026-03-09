<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
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

     //Exibe o formulário de cadastro (a janela/pagina de inserção)
    public function create() {
        return view('produtos.create');
    }

    //Recebe os dados do formulario e salva no banco de dados
    public function store(Request $request) {

    // 1. Validação simples para evitar dados vazios ou duplicados
    $request->validate([
        'nome' => 'required|string|max:255',
        'descricao' => 'required|max:255',
        'preco' => 'required|numeric',
        'quantidade' => 'required|numeric',
        'tamanho' => 'required|max:255',
        'cor' => 'required|string|max:255',
        ]);

        //2. Salva o novo Fornecedor
        Produto::create($request->all());

        //3. Redireciona de volta para a lista com uma mensagem de sucesso
        return redirect()->route('produto.index')->with('success', 'Pedido cadastrado com sucesso!');
    }
}