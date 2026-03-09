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

    .card-estoque {
        background: #ffffff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border-left: 6px solid #f59e0b;
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
        font-weight: 600;
        color: #b45309;
        margin-bottom: 5px;
    }

    .campo input {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #cbd5f5;
        font-size: 14px;
    }

    .campo input:focus {
        outline: none;
        border-color: #f59e0b;
    }

    .botao {
        margin-top: 15px;
        background-color: #f59e0b;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: 0.3s;
    }

    .botao:hover {
        background-color: #b45309;
    }

</style>

<x-slot name="header">
    <h2 class="titulo-header">Cadastrar Produto no Estoque</h2>
</x-slot>

<div class="container-geral">
    <div class="container-nivel-1">
        <div class="container-nivel-2">

            <div class="card-estoque">

                <!-- Formulário apontando para a rota de salvar -->
                    <form action="{{ route ('estoque.store') }}" method="POST" class="space-y-4">
                    @csrf   <!--  Obrigatório para a segurança do laravel -->


                    <div class="campo">
                        <label>Nome do Produto</label>
                        <input type="text" name="nome">
                    </div>

                    <div class="campo">
                        <label>Descrição</label>
                        <input type="text" name="descricao">
                    </div>

                    <div class="campo">
                        <label>Preço</label>
                        <input type="number" step="0.01" name="preco">
                    </div>

                    <div class="campo">
                        <label>Quantidade em Estoque</label>
                        <input type="number" name="quantidade_estoque">
                    </div>

                    <div class="campo">
                        <label>Tamanho</label>
                        <input type="text" name="tamanho">
                    </div>

                    <div class="campo">
                        <label>Cor</label>
                        <input type="text" name="cor">
                    </div>

                    <button type="submit" class="botao">
                        Cadastrar Produto
                    </button>

                </form>

            </div>

        </div>
    </div>
</div>

</x-app-layout>