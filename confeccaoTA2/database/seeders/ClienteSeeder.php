<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nome' => 'João Silva', 'email' => 'joao.silva@example.com', 'telefone' => '(11) 98888-1001', 'documento' => '123.456.789-01'],
            ['nome' => 'Maria Oliveira', 'email' => 'maria.oliveira@example.com', 'telefone' => '(21) 97777-1002', 'documento' => '234.567.890-12'],
            ['nome' => 'Carlos Santos', 'email' => 'carlos.santos@example.com', 'telefone' => '(31) 96666-1003', 'documento' => '345.678.901-23'],
            ['nome' => 'Fernanda Lima', 'email' => 'fernanda.lima@example.com', 'telefone' => '(41) 95555-1004', 'documento' => '456.789.012-34'],
            ['nome' => 'Roberto Almeida', 'email' => 'roberto.almeida@example.com', 'telefone' => '(51) 94444-1005', 'documento' => '12.345.678/0001-90'],
            ['nome' => 'Ana Souza', 'email' => 'ana.souza@example.com', 'telefone' => '(61) 93333-1006', 'documento' => '567.890.123-45'],
            ['nome' => 'Patrícia Gomes', 'email' => 'patricia.gomes@example.com', 'telefone' => '(71) 92222-1007', 'documento' => '23.456.789/0001-01'],
            ['nome' => 'Lucas Ferreira', 'email' => 'lucas.ferreira@example.com', 'telefone' => '(81) 91111-1008', 'documento' => '678.901.234-56'],
            ['nome' => 'Cliente Teste', 'email' => 'cliente@sistema.com', 'telefone' => '(11) 90000-1000', 'documento' => '789.012.345-67'],
        ];

        foreach ($clientes as $cliente) {
            Cliente::updateOrCreate(
                ['email' => $cliente['email']],
                $cliente,
            );
        }
    }
}
