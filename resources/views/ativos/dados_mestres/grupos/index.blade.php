@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-2 sm:px-4 max-w-5xl">

    {{-- 🧭 Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Lista de Grupos</h1>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            {{-- ✅ Botão Nova (somente para administrador ou gestor) --}}
            @auth
                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                    <a href="{{ route('dados_mestres.grupos.create') }}"
                        class="px-4 sm:px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-center">
                        Novo Grupo
                    </a>
                @endif
            @endauth

            {{-- 🔙 Botão Voltar --}}
            <a href="{{ route('dados_mestres.index') }}"
                class="px-4 sm:px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow hover:bg-gray-600 transition text-center">
                Voltar à Lista Geral
            </a>

            @include('ativos.dados_mestres.partials.export-buttons', ['section' => 'grupos'])
        </div>
    </div>

    {{-- 🔎 Pesquisa --}}
    <div class="bg-white p-4 rounded-xl shadow-md mb-6">
        <form method="GET" action="{{ route('dados_mestres.grupos.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar grupo..."
                   class="w-full sm:w-3/4 border-gray-300 rounded-lg p-2 text-gray-700"
                   onkeydown="if(event.key==='Enter'){this.form.submit();}">
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Pesquisar
                </button>
                <a href="{{ route('dados_mestres.grupos.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    {{-- 📋 Tabela / Cartões --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">

        @if ($grupos->isEmpty())
            <p class="text-gray-600 text-center py-4">Nenhum grupo encontrado.</p>
        @else

            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-xl min-w-[600px] text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left w-1/12">#</th>
                            <th class="px-4 py-3 text-left w-7/12">Nome</th>
                            <th class="px-4 py-3 text-right w-4/12">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($grupos as $index => $grupo)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $index + 1 + ($grupos->currentPage()-1)*$grupos->perPage() }}
                                </td>
                                <td class="px-4 py-3 text-gray-800 font-medium">{{ $grupo->Nome }}</td>
                                <td class="px-4 py-3">
                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <div class="flex justify-end items-center gap-3 flex-nowrap">
                                                <a href="{{ route('dados_mestres.grupos.edit', $grupo->GrupoId) }}"
                                                   class="px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition whitespace-nowrap">
                                                    Editar
                                                </a>
                                                <form action="{{ route('dados_mestres.grupos.destroy', $grupo->GrupoId) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja eliminar este grupo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition whitespace-nowrap">
                                                        Eliminar
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
                @foreach ($grupos as $index => $grupo)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600 font-medium text-sm">#{{ $index + 1 + ($grupos->currentPage()-1)*$grupos->perPage() }}</span>
                        </div>
                        <p class="text-gray-800 font-semibold text-sm mb-2 truncate">{{ $grupo->Nome }}</p>

                        <div class="flex gap-2">
                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                    <a href="{{ route('dados_mestres.grupos.edit', $grupo->GrupoId) }}"
                                       class="w-1/2 py-2 bg-yellow-500 text-white text-center rounded-lg text-sm hover:bg-yellow-600 transition">
                                        Editar
                                    </a>
                                    <form action="{{ route('dados_mestres.grupos.destroy', $grupo->GrupoId) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja eliminar este grupo?');"
                                          class="w-1/2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 📄 Paginação --}}
            <div class="mt-4">
                {{ $grupos->appends(request()->query())->links() }}
            </div>

        @endif
    </div>
</div>
@endsection
