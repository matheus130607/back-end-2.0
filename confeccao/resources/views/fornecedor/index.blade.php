<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Fornecedores
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4">
                <a href="{{ route('fornecedores.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    Novo Fornecedor
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-800 p-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left">Nome</th>
                                <th class="border px-4 py-2 text-left">Telefone</th>
                                <th class="border px-4 py-2 text-left">Empresa</th>
                                <th class="border px-4 py-2 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fornecedores as $fornecedor)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-4 py-2">
                                        {{ $fornecedor->nome }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{ $fornecedor->telefone }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{ $fornecedor->empresa }}
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        <a href="{{ route('fornecedores.edit', $fornecedor) }}"
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                            Editar
                                        </a>

                                        <form action="{{ route('fornecedores.destroy', $fornecedor) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                                Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        Nenhum fornecedor cadastrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>