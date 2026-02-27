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
            max-width: 1200px;
        }

        .container-nivel-2 {
            width: 100%;
        }

        .container-nivel-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .card-cliente {
            background: #ffffff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transition: 0.3s ease-in-out;
            border-left: 6px solid #2563eb;
        }

        .card-cliente:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }

        .card-cliente h3 {
            margin: 0 0 10px;
            color: #1e3a8a;
            font-size: 20px;
        }

        .card-cliente p {
            margin: 5px 0;
            color: #475569;
            font-size: 15px;
        }

        .titulo-header {
            color: white;
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
    </style>

    <x-slot name="header">
        <h2 class="titulo-header">Nossa Confecção</h2>
    </x-slot>

    <div class="container-geral">
        <div class="container-nivel-1">
            <div class="container-nivel-2">
                <div class="container-nivel-3">
                    @foreach ($clientes as $cliente)
                        <div class="card-cliente">
                            <h3>{{ $cliente->nome }}</h3>
                            <p>CPF: {{ $cliente->cpf }}</p>
                            <p>Telefone: {{ $cliente->telefone }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</x-app-layout>