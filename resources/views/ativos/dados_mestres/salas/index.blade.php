@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-2 sm:px-4 max-w-6xl">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Lista de Salas</h1>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            @auth
                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                    <a href="{{ route('dados_mestres.salas.create') }}"
                       class="px-4 sm:px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-center">
                        Nova Sala
                    </a>
                @endif
            @endauth

            <a href="{{ route('dados_mestres.index') }}"
               class="px-4 sm:px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow hover:bg-gray-600 transition text-center">
               Voltar à Lista Geral
            </a>

            @include('ativos.dados_mestres.partials.export-buttons', ['section' => 'salas'])
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white p-3 sm:p-4 rounded-xl shadow-md mb-6">
        <form method="GET" action="{{ route('dados_mestres.salas.index') }}" id="filtroForm" class="grid grid-cols-1 sm:grid-cols-5 gap-3 sm:gap-4">

            {{-- Província --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Província</label>
                <select name="provincia" id="provincia" class="w-full border-gray-300 rounded-lg p-2 text-gray-700 text-sm">
                    <option value="">Todas</option>
                    @foreach($provincias as $provincia)
                        <option value="{{ $provincia->ProvinciaId }}" {{ request('provincia') == $provincia->ProvinciaId ? 'selected' : '' }}>
                            {{ $provincia->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Edifício --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Edifício</label>
                <select name="edificio" id="edificio" class="w-full border-gray-300 rounded-lg p-2 text-gray-700 text-sm">
                    <option value="">Todos</option>
                    @foreach($edificios as $edificio)
                        <option value="{{ $edificio->EdificioId }}" {{ request('edificio') == $edificio->EdificioId ? 'selected' : '' }}>
                            {{ $edificio->Nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Piso --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Piso</label>
                <select name="piso" id="piso" class="w-full border-gray-300 rounded-lg p-2 text-gray-700 text-sm">
                    <option value="">Todos</option>
                    @foreach($pisos as $piso)
                        <option value="{{ $piso->PisoId }}" {{ request('piso') == $piso->PisoId ? 'selected' : '' }}>
                            @if(!$piso->EdificioId)
                                {{ $piso->Nome }}
                            @else
                                {{ $piso->edificio->Nome ?? '' }} / {{ $piso->Nome }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Pesquisa livre --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesquisa</label>
                <input type="text" name="q" placeholder="Pesquisar por nome, piso, edifício..."
                       value="{{ request('q') }}"
                       class="w-full border-gray-300 rounded-lg p-2 text-gray-700 text-sm"
                       onkeydown="if(event.key==='Enter'){this.form.submit();}">
            </div>

            {{-- Botão Limpar --}}
            <div class="flex items-end">
                <button type="button" id="limparFiltros"
                        class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg shadow hover:bg-gray-300 transition text-sm">
                    Limpar
                </button>
            </div>
        </form>
    </div>

    {{-- Tabela / Cards --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">

        @if ($salas->isEmpty())
            <p class="text-gray-600 text-center py-4">Nenhuma sala encontrada.</p>
        @else

            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-xl min-w-[700px] text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left w-1/12">#</th>
                            <th class="px-4 py-2 text-left w-3/12">Província</th>
                            <th class="px-4 py-2 text-left w-3/12">Edifício</th>
                            <th class="px-4 py-2 text-left w-3/12">Piso</th>
                            <th class="px-4 py-2 text-left w-3/12">Sala</th>
                            <th class="px-4 py-2 text-right w-2/12">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($salas as $index => $sala)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-2 text-gray-700">{{ $index + 1 + ($salas->currentPage()-1)*$salas->perPage() }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $sala->piso->edificio->provincia->Nome ?? '—' }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $sala->piso->edificio->Nome ?? '—' }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $sala->piso->Nome ?? '—' }}</td>
                                <td class="px-4 py-2 text-gray-800 font-medium">{{ $sala->Nome }}</td>
                                <td class="px-4 py-3 text-right">
                                    @auth
                                        @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                            <div class="flex justify-end items-center gap-2 flex-nowrap">
                                                <a href="{{ route('dados_mestres.salas.edit', $sala->SalaId) }}"
                                                   class="px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition whitespace-nowrap">
                                                    Editar
                                                </a>
                                                <form action="{{ route('dados_mestres.salas.destroy', $sala->SalaId) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja eliminar esta sala?');">
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
                @foreach ($salas as $index => $sala)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600 font-medium text-sm">#{{ $index + 1 + ($salas->currentPage()-1)*$salas->perPage() }}</span>
                        </div>
                        <p class="text-gray-700 text-sm mb-1">Província: {{ $sala->piso->edificio->provincia->Nome ?? '—' }}</p>
                        <p class="text-gray-700 text-sm mb-1">Edifício: {{ $sala->piso->edificio->Nome ?? '—' }}</p>
                        <p class="text-gray-700 text-sm mb-1">Piso: {{ $sala->piso->Nome ?? '—' }}</p>
                        <p class="text-gray-800 font-medium text-sm mb-2">Sala: {{ $sala->Nome }}</p>

                        <div class="flex gap-2">
                            @auth
                                @if(in_array(auth()->user()->perfil, ['administrador','gestor']))
                                    <a href="{{ route('dados_mestres.salas.edit', $sala->SalaId) }}"
                                       class="w-1/2 py-2 bg-yellow-500 text-white text-center rounded-lg text-sm hover:bg-yellow-600 transition">
                                        Editar
                                    </a>
                                    <form action="{{ route('dados_mestres.salas.destroy', $sala->SalaId) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja eliminar esta sala?');"
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
                {{ $salas->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Script para filtros dinâmicos via AJAX --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const provinciaSelect = document.getElementById('provincia');
    const edificioSelect = document.getElementById('edificio');
    const pisoSelect = document.getElementById('piso');
    const limparBtn = document.getElementById('limparFiltros');

    provinciaSelect.addEventListener('change', function() {
        const provinciaId = this.value;
        edificioSelect.innerHTML = '<option value="">A carregar...</option>';
        pisoSelect.innerHTML = '<option value="">Todos</option>';
        if (provinciaId) {
            fetch(`/dados-mestres/edificios/por-provincia/${provinciaId}`)
                .then(res => res.json())
                .then(data => {
                    edificioSelect.innerHTML = '<option value="">Todos</option>';
                    data.forEach(e => {
                        edificioSelect.innerHTML += `<option value="${e.EdificioId}">${e.Nome}</option>`;
                    });
                });
        } else {
            edificioSelect.innerHTML = '<option value="">Todos</option>';
        }
        document.getElementById('filtroForm').submit();
    });

    edificioSelect.addEventListener('change', function() {
        const edificioId = this.value;
        pisoSelect.innerHTML = '<option value="">A carregar...</option>';
        if (edificioId) {
            fetch(`/dados-mestres/pisos/por-edificio/${edificioId}`)
                .then(res => res.json())
                .then(data => {
                    pisoSelect.innerHTML = '<option value="">Todos</option>';
                    data.forEach(p => {
                        pisoSelect.innerHTML += `<option value="${p.PisoId}">${p.Nome}</option>`;
                    });
                });
        } else {
            fetch(`/dados-mestres/pisos/todos-com-edificio`)
                .then(res => res.json())
                .then(data => {
                    pisoSelect.innerHTML = '<option value="">Todos</option>';
                    data.forEach(p => {
                        pisoSelect.innerHTML += `<option value="${p.PisoId}">${p.edificio_nome} / ${p.Nome}</option>`;
                    });
                });
        }
        document.getElementById('filtroForm').submit();
    });

    pisoSelect.addEventListener('change', function() {
        document.getElementById('filtroForm').submit();
    });

    limparBtn.addEventListener('click', function() {
        provinciaSelect.value = '';
        edificioSelect.innerHTML = '<option value="">Todos</option>';
        pisoSelect.innerHTML = '<option value="">Todos</option>';
        document.querySelector('input[name="q"]').value = '';
        document.getElementById('filtroForm').submit();
    });
});
</script>

@endsection
