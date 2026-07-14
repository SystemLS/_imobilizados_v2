@extends('layouts.app')

@section('title','Inventário — Lista de Ativos')
@section('content')
<div class="container mx-auto p-4 md:p-6">

    {{-- Título --}}
    <h1 class="text-2xl font-bold mb-6 text-gray-700">📋 Inventário — Ativos</h1>

    {{-- Botões de Ação --}}
    <div class="flex flex-col md:flex-row md:justify-between gap-3 mb-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inventario.export.pdf') }}" class="flex items-center gap-1 px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition">
                <i data-feather="file-text"></i> Exportar PDF
            </a>
            <a href="{{ route('inventario.export.excel') }}" class="flex items-center gap-1 px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                <i data-feather="file"></i> Exportar Excel
            </a>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('followup.index') }}" class="flex items-center gap-1 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <i data-feather="qr-code"></i> Novo Follow Up
            </a>
            <a href="{{ route('followup.relatorios') }}" class="flex items-center gap-1 px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition">
                <i data-feather="bar-chart-2"></i> Relatórios de Follow Up
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <form class="flex flex-col gap-3 mb-4 md:flex-row md:flex-wrap">
        <select name="provincia" class="border border-gray-300 rounded-lg px-3 py-2 w-full md:w-auto focus:ring-1 focus:ring-blue-400 focus:outline-none truncate">
            <option value="">-- Província --</option>
            @foreach($provincias as $prov)
                <option value="{{ $prov->ProvinciaId }}" @selected(request('provincia') == $prov->ProvinciaId)>{{ $prov->Nome }}</option>
            @endforeach
        </select>

        <select name="edificio" class="border border-gray-300 rounded-lg px-3 py-2 w-full md:w-auto focus:ring-1 focus:ring-blue-400 focus:outline-none truncate">
            <option value="">-- Edifício --</option>
            @foreach($edificios as $edif)
                <option value="{{ $edif->EdificioId }}" @selected(request('edificio') == $edif->EdificioId)>{{ $edif->Nome }}</option>
            @endforeach
        </select>

        <select name="grupo" class="border border-gray-300 rounded-lg px-3 py-2 w-full md:w-auto focus:ring-1 focus:ring-blue-400 focus:outline-none truncate">
            <option value="">-- Grupo --</option>
            @foreach($grupos as $g)
                <option value="{{ $g->GrupoId }}" @selected(request('grupo') == $g->GrupoId)>{{ $g->Nome }}</option>
            @endforeach
        </select>

        <select name="categoria" class="border border-gray-300 rounded-lg px-3 py-2 w-full md:w-auto focus:ring-1 focus:ring-blue-400 focus:outline-none truncate">
            <option value="">-- Categoria --</option>
            @foreach($categorias as $c)
                <option value="{{ $c->CategoriaId }}" @selected(request('categoria') == $c->CategoriaId)>{{ $c->Nome }}</option>
            @endforeach
        </select>

        {{-- Botões aplicar/limpar --}}
        <div class="flex justify-center gap-2 w-full md:w-auto">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition flex-1 text-center">
                Aplicar
            </button>
            <a href="{{ route('inventario.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition flex-1 text-center">
                Limpar
            </a>
        </div>
    </form>

    {{-- Verificação se há ativos --}}
    @if($bens->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-gray-500">
            <i data-feather="alert-circle" class="w-12 h-12 mb-4"></i>
            <p class="text-xl font-semibold">Nenhum activo encontrado</p>
        </div>
    @else
        {{-- Tabela Desktop --}}
        <div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse hidden md:table">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Etiqueta</th>
                        <th class="px-4 py-2">Nome</th>
                        <th class="px-4 py-2">Grupo</th>
                        <th class="px-4 py-2">Categoria</th>
                        <th class="px-4 py-2">Localização</th>
                        <th class="px-4 py-2">Estado</th>
                        <th class="px-4 py-2 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bens as $bem)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-4 py-2 truncate">{{ $bem->Etiqueta ?? '—' }}</td>
                        <td class="px-4 py-2 truncate">{{ $bem->Nome }}</td>
                        <td class="px-4 py-2 truncate">{{ $bem->grupo->Nome ?? '—' }}</td>
                        <td class="px-4 py-2 truncate">{{ $bem->categoria->Nome ?? '—' }}</td>
                        <td class="px-4 py-2 truncate">
                            {{ optional($bem->sala->piso->edificio->provincia)->Nome ?? '-' }} -
                            {{ optional($bem->sala->piso->edificio)->Nome ?? '-' }} -
                            {{ optional($bem->sala->piso)->Nome ?? '-' }} -
                            {{ $bem->sala->Nome ?? '-' }}
                        </td>
                        <td class="px-4 py-2 truncate">{{ $bem->estadoConservacao->Nome ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('inventario.show', $bem) }}" class="px-5 py-2 bg-yellow-400 text-white font-semibold rounded-lg hover:bg-yellow-500 transition">
                                Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Mobile Cards --}}
            <div class="md:hidden flex flex-col gap-3">
                @foreach($bens as $bem)
                    <div class="bg-gray-50 p-4 rounded-lg shadow hover:shadow-md transition">
                        <p><strong>Etiqueta:</strong> {{ $bem->Etiqueta ?? '—' }}</p>
                        <p><strong>Nome:</strong> {{ $bem->Nome }}</p>
                        <p><strong>Grupo:</strong> {{ $bem->grupo->Nome ?? '—' }}</p>
                        <p><strong>Categoria:</strong> {{ $bem->categoria->Nome ?? '—' }}</p>
                        <p><strong>Localização:</strong>
                            {{ optional($bem->sala->piso->edificio->provincia)->Nome ?? '-' }} -
                            {{ optional($bem->sala->piso->edificio)->Nome ?? '-' }} -
                            {{ optional($bem->sala->piso)->Nome ?? '-' }} -
                            {{ $bem->sala->Nome ?? '-' }}
                        </p>
                        <p><strong>Estado:</strong> {{ $bem->estadoConservacao->Nome ?? '-' }}</p>
                        <div class="flex justify-center mt-3">
                            <a href="{{ route('inventario.show', $bem) }}" class="px-6 py-2 bg-yellow-400 text-white font-semibold rounded-lg hover:bg-yellow-500 transition text-center w-full max-w-xs">
                                Ver
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginação --}}
            <div class="mt-4">
                {{ $bens->onEachSide(1)->links('pagination::tailwind') }}
            </div>
        </div>
    @endif
</div>

{{-- Feather Icons --}}
<script src="https://unpkg.com/feather-icons"></script>
<script>feather.replace();</script>

@endsection
