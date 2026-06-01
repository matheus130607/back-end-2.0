<?php

namespace Database\Seeders;

use App\Models\Fornecedor;
use Illuminate\Database\Seeder;

class FornecedorSeeder extends Seeder
{
    public function run(): void
    {
        $fornecedores = [
            [
                'empresa' => 'Fornecedor Teste',
                'documento' => '34.567.890/0001-12',
                'endereco' => 'Rua dos Testes, 100 - São Paulo/SP',
                'telefone' => '(11) 90000-2000',
            ],
            [
                'empresa' => 'Distribuidora Central Tech',
                'documento' => '45.678.901/0001-23',
                'endereco' => 'Avenida Comercial, 450 - Campinas/SP',
                'telefone' => '(19) 98888-2200',
            ],
            [
                'empresa' => 'Malharia Premium Brasil',
                'documento' => '56.789.012/0001-34',
                'endereco' => 'Rua Algodão, 88 - Blumenau/SC',
                'telefone' => '(47) 97777-3300',
            ],
        ];

        foreach ($fornecedores as $fornecedor) {
            Fornecedor::updateOrCreate(
                ['documento' => $fornecedor['documento']],
                $fornecedor,
            );
        }
    }
}
