@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-2 sm:px-4 max-w-6xl">

    {{-- ✅ Mensagem de sucesso --}}
    @if (session('success'))
        <div id="alertaSucesso" class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => document.getElementById('alertaSucesso')?.remove(), 4000);
        </script>
    @endif

    {{-- ✅ Cabeçalho com botões --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">📦 Materiais</h1>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto justify-end">
            @auth
                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                    <a href="{{ route('dados_mestres.materiais.create') }}"
                       class="px-4 sm:px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-center">
                        Novo Material
                    </a>
                @endif
            @endauth

            <a href="{{ route('dados_mestres.index') }}"
               class="px-4 sm:px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow hover:bg-gray-600 transition text-center">
               Voltar à Lista Geral
            </a>

            @include('ativos.dados_mestres.partials.export-buttons', ['section' => 'materiais'])
        </div>
    </div>

    {{-- 📋 Tabela / Cards --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">

        @if ($materiais->isEmpty())
            <p class="text-gray-600 text-center py-4">Nenhum material cadastrado.</p>
        @else
            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left w-1/12">#</th>
                            <th class="px-4 py-3 text-left w-7/12">Nome</th>
                            <th class="px-4 py-3 text-right w-4/12">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($materiais as $index => $material)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-2 text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-gray-800 font-medium truncate max-w-[300px]">{{ $material->Nome }}</td>
                                <td class="px-4 py-3">
                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <div class="flex justify-end items-center gap-3 flex-nowrap">
                                                <a href="{{ route('dados_mestres.materiais.edit', $material->MaterialId) }}"
                                                   class="px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition whitespace-nowrap">
                                                   Editar
                                                </a>
                                                <form action="{{ route('dados_mestres.materiais.destroy', $material->MaterialId) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Tens certeza que desejas excluir este material?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition whitespace-nowrap">
                                                        Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="sm:hidden grid grid-cols-1 gap-3">
                @foreach ($materiais as $index => $material)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600 font-medium text-sm">#{{ $index + 1 }}</span>
                        </div>
                        <p class="text-gray-800 font-semibold text-sm mb-2 truncate">{{ $material->Nome }}</p>

                        <div class="flex gap-2">
                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                    <a href="{{ route('dados_mestres.materiais.edit', $material->MaterialId) }}"
                                       class="w-1/2 py-2 bg-yellow-500 text-white text-center rounded-lg text-sm hover:bg-yellow-600 transition">
                                        Editar
                                    </a>
                                    <form action="{{ route('dados_mestres.materiais.destroy', $material->MaterialId) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tens certeza que desejas excluir este material?');"
                                          class="w-1/2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
                                            Excluir
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

                {{-- Links de paginação --}}
                <div class="mt-4">
                    {{ $materiais->links() }}
                </div>
        @endif
    </div>
</div>
@endsection
