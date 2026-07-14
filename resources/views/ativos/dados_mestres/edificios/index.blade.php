@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-2 sm:px-4 max-w-6xl">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Lista de Edifícios</h1>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            @auth
                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                    <a href="{{ route('dados_mestres.edificios.create') }}"
                        class="px-4 sm:px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-center">
                        Novo Edifício
                    </a>
                @endif
            @endauth

            <a href="{{ route('dados_mestres.index') }}"
                class="px-4 sm:px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow hover:bg-gray-600 transition text-center">
                Voltar à Lista Geral
            </a>

            @include('ativos.dados_mestres.partials.export-buttons', ['section' => 'edificios'])
        </div>
    </div>

    {{-- Filtros e Pesquisa --}}
    <form method="GET" action="{{ route('dados_mestres.edificios.index') }}"
          class="bg-white p-3 sm:p-4 rounded-xl shadow mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full">
            <select name="provincia" class="px-3 py-2 border rounded-lg w-full sm:w-64 text-sm">
                <option value="">Todas as Províncias</option>
                @foreach($provincias as $provincia)
                    <option value="{{ $provincia->ProvinciaId }}"
                        {{ request('provincia') == $provincia->ProvinciaId ? 'selected' : '' }}>
                        {{ $provincia->Nome }}
                    </option>
                @endforeach
            </select>

            <input type="text" name="search" placeholder="Pesquisar por nome do edifício..."
                   value="{{ request('search') }}"
                   class="px-3 py-2 border rounded-lg w-full sm:w-96 text-sm">
        </div>

        <div class="flex gap-2 mt-2 sm:mt-0">
            <button type="submit"
                    class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition text-sm">
                Filtrar
            </button>
            <a href="{{ route('dados_mestres.edificios.index') }}"
               class="px-3 sm:px-4 py-2 bg-gray-300 text-gray-800 rounded-lg shadow hover:bg-gray-400 transition text-sm">
               Limpar
            </a>
        </div>
    </form>

    {{-- Mensagens --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 shadow text-sm">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4 shadow text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabela / Cards --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">

        @if ($edificios->isEmpty())
            <p class="text-gray-600 text-center py-4 text-sm">Nenhum edifício cadastrado.</p>
        @else

            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-xl min-w-[700px] text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700 uppercase">
                        <tr>
                            <th class="py-3 px-4 text-left w-1/12">#</th>
                            <th class="py-3 px-4 text-left w-5/12">Nome</th>
                            <th class="py-3 px-4 text-left w-4/12">Província</th>
                            <th class="py-3 px-4 text-right w-2/12">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($edificios as $index => $edificio)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-4 text-gray-700">
                                    {{ $index + 1 + ($edificios->currentPage()-1)*$edificios->perPage() }}
                                </td>
                                <td class="py-3 px-4 text-gray-800 font-medium">{{ $edificio->Nome }}</td>
                                <td class="py-3 px-4 text-gray-700">{{ $edificio->provincia->Nome ?? '-' }}</td>
                                <td class="py-3 px-4 text-right">
                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <div class="flex justify-end items-center gap-3">
                                                <a href="{{ route('dados_mestres.edificios.edit', $edificio->EdificioId) }}"
                                                   class="px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition whitespace-nowrap">
                                                    Editar
                                                </a>
                                                <form action="{{ route('dados_mestres.edificios.destroy', $edificio->EdificioId) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja eliminar este edifício?');">
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
                @foreach ($edificios as $index => $edificio)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600 font-medium text-sm">#{{ $index + 1 + ($edificios->currentPage()-1)*$edificios->perPage() }}</span>
                        </div>
                        <p class="text-gray-800 font-semibold text-sm mb-2 truncate">{{ $edificio->Nome }}</p>
                        <p class="text-gray-700 text-sm mb-2">{{ $edificio->provincia->Nome ?? '-' }}</p>

                        <div class="flex gap-2">
                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                    <a href="{{ route('dados_mestres.edificios.edit', $edificio->EdificioId) }}"
                                       class="w-1/2 py-2 bg-yellow-500 text-white text-center rounded-lg text-sm hover:bg-yellow-600 transition">
                                        Editar
                                    </a>
                                    <form action="{{ route('dados_mestres.edificios.destroy', $edificio->EdificioId) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja eliminar este edifício?');"
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

            {{-- Paginação --}}
            <div class="mt-4">
                {{ $edificios->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
