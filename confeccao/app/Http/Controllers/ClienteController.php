<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cliente;
class ClienteController extends Controller
{
    public function index() {
        try {
            $clientes = \App\Models\Cliente::all(); // Busca todos os clientes
        } catch (\Exception $e) {
            // se houver problema com o banco (ex: driver ausente), exibir dados estáticos
            $clientes = collect([
                (object)[
                    'nome' => 'Cliente Exemplo',
                    'cpf' => '000.000.000-00',
                    'telefone' => '(00) 0000-0000',
                ],
            ]);
        }
        return view('clientes.index', compact('clientes'));
    }

    //Exibe o formulário de cadastro (a janela/pagina de inserção)
    public function create() {
        return view('clientes.create');
    }

    //Recebe os dados do formulpario e salva no banco de dados
    public function store(Request $request) {

    // 1. Validação simples para evitar dados vazios ou duplicados
    $request->validate([
        'nome' => 'required|string|max:255',
        'cpf' => 'required|string|unique:clientes',
        'telefone' => 'required|string',
        'reserva' => 'required|numeric',
        ]);

        //2. Salva o novo cliente
        Cliente::create($request->all());

        //3. Redireciona de volta para a lista com uma mensagem de sucesso
        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
    }
}
