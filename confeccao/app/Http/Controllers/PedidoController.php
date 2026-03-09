<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;

class PedidoController extends Controller
{
    // Mostra a lista de pedidos
    public function index()
    {
        try {
            $pedidos = Pedido::all(); // busca todos os pedidos
        } catch (\Exception $e) {
            // caso ocorra erro no banco
            $pedidos = collect([
                (object)[
                    'nome' => 'Pedido Exemplo',
                    'numero' => 0,
                    'preco' => 0.00,
                    'status' => 'pendente',
                    'quantidade' => 0,
                ],
            ]);
        }

        return view('pedidos.index', compact('pedidos'));
    }

    // Exibe o formulário de cadastro
    public function create()
    {
        return view('pedidos.create');
    }

    // Recebe os dados do formulário e salva no banco
    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'nome' => 'required|string|max:150',
            'numero' => 'required|numeric',
            'preco' => 'required|numeric',
            'status' => 'required|in:pendente,pago,cancelado',
            'quantidade' => 'required|numeric',
        ]);

        // Cria o pedido no banco
        Pedido::create([
            'nome' => $request->nome,
            'numero' => $request->numero,
            'preco' => $request->preco,
            'status' => $request->status,
            'quantidade' => $request->quantidade,
        ]);

        // Redireciona para a lista de pedidos
        return redirect()->route('pedido.index')
            ->with('success', 'Pedido cadastrado com sucesso!');
    }
}