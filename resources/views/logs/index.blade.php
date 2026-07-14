@extends('layouts.app')

@section('title', 'Logs do Sistema')
@section('page-title', 'Últimos Logs')

@section('content')
<div class="container mx-auto py-8 px-2 sm:px-4 max-w-7xl">

    {{-- Filtros e exportação --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">

        {{-- Formulário de filtros --}}
        <form action="{{ route('logs.index') }}" method="GET" class="flex flex-wrap gap-3 items-end w-full md:w-auto">

            {{-- Data início --}}
            <div class="flex flex-col">
                <label for="data_inicio" class="text-gray-700 text-sm mb-1">Data Início</label>
                <input type="date" id="data_inicio" name="data_inicio"
                       value="{{ request('data_inicio') }}"
                       class="border rounded px-2 sm:px-3 py-1 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
            </div>

            {{-- Data fim --}}
            <div class="flex flex-col">
                <label for="data_fim" class="text-gray-700 text-sm mb-1">Data Fim</label>
                <input type="date" id="data_fim" name="data_fim"
                       value="{{ request('data_fim') }}"
                       class="border rounded px-2 sm:px-3 py-1 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
            </div>

            {{-- Filtro por usuário --}}
            <div class="flex flex-col">
                <label for="usuario_id" class="text-gray-700 text-sm mb-1">Usuário</label>
                <select id="usuario_id" name="usuario_id"
                        class="border rounded px-2 sm:px-3 py-1 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    <option value="">Todos</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ request('usuario_id') == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Botões de filtro e limpar --}}
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-3 sm:px-4 py-1 sm:py-2 rounded text-sm sm:text-base hover:bg-blue-700 transition">
                    Filtrar
                </button>
                <a href="{{ route('logs.index') }}" class="bg-gray-500 text-white px-3 sm:px-4 py-1 sm:py-2 rounded text-sm sm:text-base hover:bg-gray-600 transition">
                    Limpar
                </a>
            </div>
        </form>

        {{-- Botões de exportação --}}
        <div class="flex flex-wrap gap-2 mt-3 md:mt-0">
            <a href="{{ route('logs.export', ['format' => 'excel'] + request()->query()) }}" class="flex items-center gap-1 px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                <i data-feather="file"></i> Exportar Excel
            </a>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('alert'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 rounded shadow mb-4 text-sm sm:text-base">
            {{ session('alert') }}
        </div>
    @endif

    {{-- Tabela / Cards --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">

        @if ($logs->isEmpty())
            <p class="text-gray-600 text-center py-4 text-sm sm:text-base">Nenhum log encontrado.</p>
        @else

            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-xl table-auto text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left w-1/12">#</th>
                            <th class="px-4 py-2 text-left w-3/12">Usuário</th>
                            <th class="px-4 py-2 text-left w-4/12">Evento</th>
                            <th class="px-4 py-2 text-left w-4/12">Descrição</th>
                            <th class="px-4 py-2 text-left w-2/12">Horário</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($logs as $index => $log)
                            <tr class="hover:bg-gray-50 transition
                                       {{ in_array($log->evento, ['Exclusão crítica', 'Alteração de permissão']) ? 'bg-red-50' : '' }}">
                                <td class="px-4 py-2 text-gray-700">{{ $logs->firstItem() + $index }}</td>
                                <td class="px-4 py-2 text-gray-800 font-medium">{{ $log->usuario?->name ?? 'Sistema' }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $log->evento }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $log->descricao }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="sm:hidden grid grid-cols-1 gap-3">
                @foreach ($logs as $index => $log)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600 font-medium text-sm">#{{ $logs->firstItem() + $index }}</span>
                            <span class="text-gray-600 text-xs sm:text-sm">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <p class="text-gray-800 font-semibold text-sm truncate mb-1">Usuário: {{ $log->usuario?->name ?? 'Sistema' }}</p>
                        <p class="text-gray-700 text-sm truncate mb-1">Evento: {{ $log->evento }}</p>
                        <p class="text-gray-700 text-sm truncate">Descrição: {{ $log->descricao }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Paginação --}}
            <div class="mt-4">
                {{ $logs->links() }}
            </div>

        @endif
    </div>
</div>
@endsection
