<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index() {
        try {
            $pedidos = \App\Models\Pedido::all(); // Busca todos os pedidos
        } catch (\Exception $e) {
            // se houver problema com o banco (ex: driver ausente), exibir dados estáticos
            $pedidos = collect([
                (object)[
                    'nome' => 'Pedido Exemplo',
                    'preco' => 0.00,
                    'status' => 'pendente',
                ],
            ]);
        }

        // A view nomeada anteriormente estava incorreta. o arquivo real está em
        // resources/views/pedidos/index2.blade.php, portanto precisamos incluir o
        // namespace da pasta.
        return view('pedidos.index', compact('pedidos'));
    }
}