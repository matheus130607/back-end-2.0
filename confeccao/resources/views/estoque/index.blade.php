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

        .card-estoque {
            background: #ffffff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transition: 0.3s ease-in-out;
            border-left: 6px solid #f59e0b;
        }

        .card-estoque:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }

        .card-estoque h3 {
            margin: 0 0 10px;
            color: #b45309;
            font-size: 20px;
        }

        .card-estoque p {
            margin: 5px 0;
            color: #475569;
            font-size: 15px;
        }

        .quantidade {
            font-weight: bold;
            padding: 6px 10px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 8px;
        }

        .baixo {
            background-color: #ef4444;
            color: white;
        }

        .medio {
            background-color: #facc15;
            color: #78350f;
        }

        .alto {
            background-color: #22c55e;
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
        <h2 class="titulo-header">Controle de Estoque</h2>
    </x-slot>

    <div class="container-geral">
        <div class="container-nivel-1">
            <div class="container-nivel-2">
                <div class="container-nivel-3">
                    @foreach ($estoques as $estoque)
                        <div class="card-estoque">
                            <h3>{{ $estoque->nome }}</h3>

                            <p>Quantidade disponível:</p>

                            <span class="quantidade 
                                @if($estoque->quantidade <= 10)
                                    baixo
                                @elseif($estoque->quantidade <= 30)
                                    medio
                                @else
                                    alto
                                @endif
                            ">
                                {{ $estoque->quantidade }} unidades
                            </span>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</x-app-layout>