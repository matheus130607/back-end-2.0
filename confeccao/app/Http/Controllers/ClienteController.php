<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index() {
        try {
            $clientes = \App\Models\clientes::all(); // Busca todos os clientes
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
}
