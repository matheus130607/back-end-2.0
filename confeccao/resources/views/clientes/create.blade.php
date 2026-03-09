<x-app-layout>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e3a8a, #3b82f6);
            background-attachment: fixed;
        }

        .container-geral {
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .container-nivel-1 {
            width: 100%;
            max-width: 800px;
        }

        .container-nivel-2 {
            width: 100%;
        }

        .form-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-left: 6px solid #2563eb;
        }

        .titulo-header {
            color: black;
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .campo {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .campo label {
            margin-bottom: 5px;
            color: #1e3a8a;
            font-weight: 600;
        }

        .campo input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #cbd5f5;
            font-size: 14px;
        }

        .campo input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .botao {
            margin-top: 15px;
            background-color: #2563eb;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .botao:hover {
            background-color: #1e40af;
        }

    </style>

    <x-slot name="header">
        <h2 class="titulo-header">Cadastro de Clientes</h2>
    </x-slot>

    <div class="container-geral">
        <div class="container-nivel-1">
            <div class="container-nivel-2">

                <div class="form-card">

                    <!-- Formulário apontando para a rota de salvar -->
                    <form action="{{ route ('clientes.store') }}" method="POST" class="space-y-4">
                    @csrf   <!--  Obrigatório para a segurança do laravel -->

                        <div class="campo">
                            <label>Nome</label>
                            <input type="text" name="nome" placeholder="Digite o nome do cliente">
                        </div>

                        <div class="campo">
                            <label>CPF</label>
                            <input type="text" name="cpf" placeholder="Digite o CPF">
                        </div>

                        <div class="campo">
                            <label>Telefone</label>
                            <input type="text" name="telefone" placeholder="Digite o telefone">
                        </div>

                        <div class="campo">
                            <label>Reserva</label>
                            <input type="text" name="reserva" placeholder="Digite a reserva">
                        </div>

                        <button type="submit" class="botao">
                            Cadastrar Cliente
                        </button>

                    </form>

                </div>

            </div>
        </div>
    </div>

</x-app-layout>