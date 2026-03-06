<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background: linear-gradient(135deg, #0f172a, #1e3a8a, #3b82f6); min-height: 80vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10">

                <div style="text-align: center; margin-bottom: 40px;">
                    <h3 style="font-size: 22px; font-weight: bold; color: #1e3a8a;">
                        Painel da Confecção
                    </h3>
                    <p style="color: #64748b;">Acesse as áreas do sistema</p>
                </div>

                <div style="
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 30px;
                ">

                    <!-- Botão Clientes -->
                    <a href="/clientes" style="
                        background: #2563eb;
                        color: white;
                        padding: 30px;
                        border-radius: 12px;
                        text-align: center;
                        text-decoration: none;
                        font-weight: bold;
                        font-size: 18px;
                        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
                        transition: 0.3s;
                    " onmouseover="this.style.transform='translateY(-5px)'" 
                       onmouseout="this.style.transform='translateY(0)'">
                        👥 Clientes
                    </a>

                    <!-- Botão Pedidos -->
                    <a href="/pedidos" style="
                        background: #16a34a;
                        color: white;
                        padding: 30px;
                        border-radius: 12px;
                        text-align: center;
                        text-decoration: none;
                        font-weight: bold;
                        font-size: 18px;
                        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
                        transition: 0.3s;
                    " onmouseover="this.style.transform='translateY(-5px)'" 
                       onmouseout="this.style.transform='translateY(0)'">
                        📦 Pedidos
                    </a>

                    <!-- Botão Estoque -->
                    <a href="/estoque" style="
                        background: #f59e0b;
                        color: white;
                        padding: 30px;
                        border-radius: 12px;
                        text-align: center;
                        text-decoration: none;
                        font-weight: bold;
                        font-size: 18px;
                        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
                        transition: 0.3s;
                    " onmouseover="this.style.transform='translateY(-5px)'" 
                       onmouseout="this.style.transform='translateY(0)'">
                        🏬 Estoque
                    </a>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>