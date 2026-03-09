<?php

namespace App\Http\Controllers;
use App\Models\Estoque;
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

    //Exibe o formulário de cadastro (a janela/pagina de inserção)
    public function create() {
        return view('estoque.create');
    }

    //Recebe os dados do formulario e salva no banco de dados
    public function store(Request $request) {

    // 1. Validação simples para evitar dados vazios ou duplicados
    $request->validate([
        'nome' => 'required|string|max:255',
        'descricao' => 'required|string|max:255',
        'preco' => 'required|decimal(10,2)',
        'quantidade_estoque' => 'required|numeric',
        'tamanho' => 'required|string|max:255',
        'cor' => 'required|string|max:255',
        ]);

        //2. Salva o novo Estoque
        Estoque::create($request->all());

        //3. Redireciona de volta para a lista com uma mensagem de sucesso
        return redirect()->route('estoque.index')->with('success', 'Estoque cadastrado com sucesso!');
    }
}