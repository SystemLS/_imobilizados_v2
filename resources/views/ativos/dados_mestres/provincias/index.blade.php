@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-2 sm:px-4 max-w-5xl">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Lista de Províncias</h1>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            @auth
                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                    <a href="{{ route('dados_mestres.provincias.create') }}"
                        class="px-4 sm:px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-center">
                        Nova Província
                    </a>
                @endif
            @endauth

            <a href="{{ route('dados_mestres.index') }}"
                class="px-4 sm:px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow hover:bg-gray-600 transition text-center">
                Voltar à Lista Geral
            </a>

            @include('ativos.dados_mestres.partials.export-buttons', ['section' => 'provincias'])
        </div>
    </div>

    {{-- Tabela / Cartões --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">

        @if ($provincias->isEmpty())
            <p class="text-gray-600 text-center py-4">Nenhuma província cadastrada.</p>
        @else

            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-xl min-w-[600px] text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left w-1/12">#</th>
                            <th class="px-4 py-2 text-left w-7/12">Nome</th>
                            <th class="px-4 py-2 text-right w-4/12">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($provincias as $index => $provincia)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-2 text-gray-700">{{ $index + 1 + ($provincias->currentPage()-1)*$provincias->perPage() }}</td>
                                <td class="px-4 py-2 text-gray-800 font-medium truncate max-w-[300px]">{{ $provincia->Nome }}</td>
                                <td class="px-4 py-3">
                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <div class="flex justify-end items-center gap-3 flex-nowrap">
                                                <a href="{{ route('dados_mestres.provincias.edit', $provincia->ProvinciaId) }}"
                                                   class="px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition whitespace-nowrap">
                                                    Editar
                                                </a>
                                                <form action="{{ route('dados_mestres.provincias.destroy', $provincia->ProvinciaId) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja eliminar esta província?');">
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
                @foreach ($provincias as $index => $provincia)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600 font-medium text-sm">#{{ $index + 1 + ($provincias->currentPage()-1)*$provincias->perPage() }}</span>
                        </div>
                        <p class="text-gray-800 font-semibold text-sm mb-2 truncate">{{ $provincia->Nome }}</p>

                        <div class="flex gap-2">
                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                    <a href="{{ route('dados_mestres.provincias.edit', $provincia->ProvinciaId) }}"
                                       class="w-1/2 py-2 bg-yellow-500 text-white text-center rounded-lg text-sm hover:bg-yellow-600 transition">
                                        Editar
                                    </a>
                                    <form action="{{ route('dados_mestres.provincias.destroy', $provincia->ProvinciaId) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja eliminar esta província?');"
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
                {{ $provincias->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
