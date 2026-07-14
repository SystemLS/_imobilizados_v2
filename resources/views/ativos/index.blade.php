@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 px-2 sm:px-4">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Todos os ativos</h1>

        <div class="flex flex-wrap gap-2">
            @auth
                <a href="{{ route('ativos.export.pdf') }}"
                   class="flex items-center gap-1 px-3 sm:px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition text-sm">
                    <i data-feather="file-pdf" style="width: 16px; height: 16px;"></i> PDF
                </a>
                <a href="{{ route('ativos.export.excel') }}"
                   class="flex items-center gap-1 px-3 sm:px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition text-sm">
                    <i data-feather="file" style="width: 16px; height: 16px;"></i> Excel
                </a>
                @if(in_array(auth()->user()->perfil, ['administrador','gestor','tecnico_cadastro','tecnico_contabilidade']))
                    <a href="{{ route('ativos.create') }}"
                       class="px-4 sm:px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition text-center">
                       Cadastrar Activo
                    </a>
                @endif
            @endauth
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('ativos.index') }}" id="filtros-form"
          class="mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-2 sm:gap-3 bg-gray-50 p-3 sm:p-4 rounded-xl shadow-sm">

        <input type="text" name="search" id="search" value="{{ request('search') }}"
               class="border rounded-xl px-2 py-2 sm:px-3 sm:py-2 w-full shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition text-sm sm:text-base"
               placeholder="Buscar por Nome, Etiqueta ou Categoria...">

        <select name="edificio_id" class="border rounded-xl px-2 py-2 sm:px-3 sm:py-2 w-full filtro-auto shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition text-sm sm:text-base">
            <option value="">Todos os Edifícios</option>
            @foreach($edificios as $edificio)
                <option value="{{ $edificio->EdificioId }}" {{ request('edificio_id') == $edificio->EdificioId ? 'selected' : '' }}>
                    {{ $edificio->Nome }}
                </option>
            @endforeach
        </select>

        <select name="sala_id" class="border rounded-xl px-2 py-2 sm:px-3 sm:py-2 w-full filtro-auto shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition text-sm sm:text-base">
            <option value="">Todas as Salas</option>
            @foreach($salas as $sala)
                <option value="{{ $sala->SalaId }}" {{ request('sala_id') == $sala->SalaId ? 'selected' : '' }}>
                    {{ $sala->Nome }}
                </option>
            @endforeach
        </select>

        <select name="ordem_tempo" class="border rounded-xl px-2 py-2 sm:px-3 sm:py-2 w-full filtro-auto shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition text-sm sm:text-base">
            <option value="">Ordenar por Tempo</option>
            <option value="mais_recente" {{ request('ordem_tempo') == 'mais_recente' ? 'selected' : '' }}>Mais recente</option>
            <option value="mais_antigo" {{ request('ordem_tempo') == 'mais_antigo' ? 'selected' : '' }}>Mais antigo</option>
        </select>

        <select name="ordem_nome" class="border rounded-xl px-2 py-2 sm:px-3 sm:py-2 w-full filtro-auto shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition text-sm sm:text-base">
            <option value="">Ordem Alfabética</option>
            <option value="asc" {{ request('ordem_nome') == 'asc' ? 'selected' : '' }}>A → Z</option>
            <option value="desc" {{ request('ordem_nome') == 'desc' ? 'selected' : '' }}>Z → A</option>
        </select>

        <button type="button" id="limpar-filtros"
                class="px-3 py-2 sm:px-4 sm:py-2 bg-gray-400 text-white rounded-xl shadow hover:bg-gray-500 transition text-sm sm:text-base w-full">
            Limpar
        </button>
    </form>

    {{-- Tabela Desktop --}}
    <div class="bg-white rounded-xl shadow-lg p-3 sm:p-5 overflow-x-auto">
        @if($ativos->isEmpty())
            <p class="text-gray-600 text-center py-4">Nenhum ativo cadastrado.</p>
        @else
            <div class="hidden sm:block">
                <table class="min-w-full border-collapse rounded-xl text-sm sm:text-base">
                    <thead class="bg-gray-100">
                        <tr class="text-gray-600 text-left uppercase">
                            <th class="py-2 px-2 sm:py-3 sm:px-4 border-b text-left">Nome</th>
                            <th class="py-2 px-2 sm:py-3 sm:px-4 border-b border-l text-left">Etiqueta</th>
                            <th class="py-2 px-2 sm:py-3 sm:px-4 border-b border-l text-left">Categoria</th>
                            <th class="py-2 px-2 sm:py-3 sm:px-4 border-b border-l text-left">Localização</th>
                            <th class="py-2 px-2 sm:py-3 sm:px-4 border-b border-l text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($ativos as $ativo)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-1 px-1 sm:py-3 sm:px-4">{{ $ativo->Nome }}</td>
                            <td class="py-1 px-1 sm:py-3 sm:px-4 border-l">{{ $ativo->Etiqueta ?? '-' }}</td>
                            <td class="py-1 px-1 sm:py-3 sm:px-4 border-l">{{ $ativo->subcategoria->Nome ?? '-' }}</td>
                            <td class="py-1 px-1 sm:py-3 sm:px-4 border-l">{{ $ativo->sala->Nome ?? '-' }}</td>
                            <td class="py-1 px-1 sm:py-3 sm:px-4 border-l">
                                <div class="flex justify-end gap-2 flex-nowrap">
                                    <a href="{{ route('ativos.show', $ativo->BemId) }}"
                                       class="px-3 py-1 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition text-xs sm:text-sm">
                                       Ver
                                    </a>

                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor','tecnico_cadastro','tecnico_contabilidade']))
                                            <a href="{{ route('ativos.edit', $ativo->BemId) }}"
                                               class="px-3 py-1 bg-yellow-400 text-white rounded-lg shadow hover:bg-yellow-500 transition text-xs sm:text-sm">
                                               Editar
                                            </a>
                                        @endif
                                    @endauth

                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <form action="{{ route('ativos.destroy', $ativo->BemId) }}" method="POST"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este ativo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition text-xs sm:text-sm">
                                                    Excluir
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="sm:hidden grid grid-cols-1 gap-3">
                @foreach($ativos as $ativo)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <p class="text-gray-800 font-semibold text-sm mb-1">Nome: {{ $ativo->Nome }}</p>
                        <p class="text-gray-700 text-sm mb-1">Etiqueta: {{ $ativo->Etiqueta ?? '-' }}</p>
                        <p class="text-gray-700 text-sm mb-1">Categoria: {{ $ativo->subcategoria->Nome ?? '-' }}</p>
                        <p class="text-gray-700 text-sm mb-2">Localização: {{ $ativo->sala->Nome ?? '-' }}</p>

                        <div class="flex gap-2">
                            <a href="{{ route('ativos.show', $ativo->BemId) }}"
                               class="w-1/2 py-2 bg-blue-600 text-white text-center rounded-lg text-sm hover:bg-blue-700 transition">
                               Ver
                            </a>

                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor','tecnico_cadastro','tecnico_contabilidade']))
                                    <a href="{{ route('ativos.edit', $ativo->BemId) }}"
                                       class="w-1/2 py-2 bg-yellow-400 text-white text-center rounded-lg text-sm hover:bg-yellow-500 transition">
                                       Editar
                                    </a>
                                @endif
                            @endauth

                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                    <form action="{{ route('ativos.destroy', $ativo->BemId) }}" method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja excluir este ativo?');"
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

            {{-- Paginação --}}
            <div class="mt-4">
                {{ $ativos->links() }}
            </div>
        @endif
    </div>

    {{-- JS --}}
    <script>
        document.querySelectorAll('.filtro-auto').forEach(el => el.addEventListener('change', () => document.getElementById('filtros-form').submit()));
        let timer;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => document.getElementById('filtros-form').submit(), 700);
        });
        document.getElementById('limpar-filtros').addEventListener('click', function() {
            document.getElementById('search').value = '';
            document.querySelectorAll('.filtro-auto').forEach(el => el.value = '');
            document.getElementById('filtros-form').submit();
        });
    </script>
</div>
@endsection
