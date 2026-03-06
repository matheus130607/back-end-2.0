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

        .card-pedido {
            background: #ffffff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transition: 0.3s ease-in-out;
            border-left: 6px solid #16a34a;
        }

        .card-pedido:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }

        .card-pedido h3 {
            margin: 0 0 10px;
            color: #166534;
            font-size: 20px;
        }

        .card-pedido p {
            margin: 5px 0;
            color: #475569;
            font-size: 15px;
        }

        .status {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 8px;
        }

        .status-pendente {
            background-color: #facc15;
            color: #78350f;
        }

        .status-em_producao {
            background-color: #3b82f6;
            color: white;
        }

        .status-pronto {
            background-color: #22c55e;
            color: white;
        }

        .status-entregue {
            background-color: #16a34a;
            color: white;
        }

        .status-cancelado {
            background-color: #ef4444;
            color: white;
        }

        .titulo-header {
            color: black;
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
    </style>

    <x-slot name="header">
        <h2 class="titulo-header">Pedidos da Confecção</h2>
    </x-slot>

    <div class="container-geral">
        <div class="container-nivel-1">
            <div class="container-nivel-2">
                <div class="container-nivel-3">
                    @foreach ($pedidos as $pedido)
                        <div class="card-pedido">
                            <h3>{{ $pedido->nome }}</h3>
                            <p>Preço: R$ {{ number_format($pedido->preco, 2, ',', '.') }}</p>

                            <span class="status status-{{ $pedido->status }}">
                                {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</x-app-layout>