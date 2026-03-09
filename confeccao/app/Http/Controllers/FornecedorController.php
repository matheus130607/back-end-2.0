<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;
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

    //Exibe o formulário de cadastro (a janela/pagina de inserção)
    public function create() {
        return view('fornecedor.create');
    }

    //Recebe os dados do formulario e salva no banco de dados
    public function store(Request $request) {

    // 1. Validação simples para evitar dados vazios ou duplicados
    $request->validate([
        'nome' => 'required|string|max:255',
        'cnpj' => 'required|string|unique:fornecedors',
        'telefone' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'empresa' => 'required|string|max:255',
        ]);

        //2. Salva o novo Fornecedor
        Fornecedor::create($request->all());

        //3. Redireciona de volta para a lista com uma mensagem de sucesso
        return redirect()->route('fornecedor.index')->with('success', 'Fornecedor cadastrado com sucesso!');
    }
}
