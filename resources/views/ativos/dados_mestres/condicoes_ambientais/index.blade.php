@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-2 sm:px-4 max-w-5xl">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">🌿 Condições Ambientais</h1>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            @auth
                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                    <a href="{{ route('dados_mestres.condicoes_ambientais.create') }}"
                       class="px-4 sm:px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-center">
                        Nova Condição Ambiental
                    </a>
                @endif
            @endauth

            <a href="{{ route('dados_mestres.index') }}"
               class="px-4 sm:px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow hover:bg-gray-600 transition text-center">
               Voltar à Lista Geral
            </a>

            @include('ativos.dados_mestres.partials.export-buttons', ['section' => 'condicoes_ambientais'])
        </div>
    </div>

    {{-- Mensagem de sucesso --}}
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm flex items-center justify-between">
            <span class="font-medium">{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">✖</button>
        </div>
    @endif

    {{-- Tabela / Cards --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">
        @if ($condicoes->isEmpty())
            <p class="text-gray-600 text-center py-4">Nenhuma condição ambiental cadastrada.</p>
        @else
            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-xl min-w-[600px] text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left w-1/12">#</th>
                            <th class="px-4 py-2 text-left w-5/12">Nome</th>
                            <th class="px-4 py-2 text-right w-3/12">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($condicoes as $index => $condicao)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-2 text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-gray-800 font-medium truncate max-w-[250px]">{{ $condicao->Nome }}</td>
                                <td class="px-4 py-3">
                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <div class="flex justify-end items-center gap-3 flex-nowrap">
                                                <a href="{{ route('dados_mestres.condicoes_ambientais.edit', $condicao->CondicaoAmbientalId) }}"
                                                   class="px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition whitespace-nowrap">
                                                   Editar
                                                </a>
                                                <form action="{{ route('dados_mestres.condicoes_ambientais.destroy', $condicao->CondicaoAmbientalId) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir esta condição ambiental?');">
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

                <div class="mt-4">
                    {{ $condicoes->links() }}
                </div>
            </div>

            {{-- Mobile Cards --}}
            <div class="sm:hidden grid grid-cols-1 gap-3">
                @foreach ($condicoes as $index => $condicao)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600 font-medium text-sm">#{{ $index + 1 }}</span>
                        </div>
                        <p class="text-gray-800 font-semibold text-sm mb-1 truncate">{{ $condicao->Nome }}</p>

                        <div class="flex gap-2">
                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                    <a href="{{ route('dados_mestres.condicoes_ambientais.edit', $condicao->CondicaoAmbientalId) }}"
                                       class="w-1/2 py-2 bg-yellow-500 text-white text-center rounded-lg text-sm hover:bg-yellow-600 transition">
                                        Editar
                                    </a>
                                    <form action="{{ route('dados_mestres.condicoes_ambientais.destroy', $condicao->CondicaoAmbientalId) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja excluir esta condição ambiental?');"
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

                <div class="mt-4">
                    {{ $condicoes->links() }}
                </div>
            </div>

        @endif
    </div>
</div>
@endsection
