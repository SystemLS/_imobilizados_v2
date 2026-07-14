@extends('layouts.app')

@section('title', 'Lista de Manutenções')
@section('page-title', 'Lista de Manutenções')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">

    {{-- Título e botão --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-3">

    <h3 class="text-xl font-semibold">Manutenções</h3>

    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">

        {{-- Exportar PDF --}}
        <a href="{{ route('manutencoes.export.pdf', request()->query()) }}"
           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-center">
           📄 Exportar PDF
        </a>

        {{-- Exportar Excel --}}
        <a href="{{ route('manutencoes.export.excel', request()->query()) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center">
           📊 Exportar Excel
        </a>

        {{-- Nova Manutenção --}}
        <a href="{{ route('manutencoes.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center">
           ➕ Nova Manutenção
        </a>

    </div>
</div>


    {{-- 🔍 FILTROS --}}
    <form method="GET" class="mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-lg shadow-sm">
        <input type="text" name="bem" placeholder="Pesquisar Bem" value="{{ request('bem') }}"
            class="border rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">

        <input type="text" name="etiqueta" placeholder="Pesquisar Etiqueta" value="{{ request('etiqueta') }}"
            class="border rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">

        <input type="text" name="tipo" placeholder="Tipo (Preventiva/Corretiva)" value="{{ request('tipo') }}"
            class="border rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">

        <input type="text" name="responsavel" placeholder="Responsável" value="{{ request('responsavel') }}"
            class="border rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">

        <select name="status" class="border rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">-- Status --</option>
            <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
            <option value="Em Andamento" {{ request('status') == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
            <option value="Concluída" {{ request('status') == 'Concluída' ? 'selected' : '' }}>Concluída</option>
            <option value="Cancelada" {{ request('status') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
        </select>

        {{-- Botões --}}
        <div class="flex gap-2 mt-2 md:mt-0 col-span-full">
            <button type="submit"
                class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition w-full md:w-auto">
                🔎 Pesquisar
            </button>
            <a href="{{ route('manutencoes.index') }}"
                class="bg-gray-300 text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-400 transition w-full md:w-auto text-center">
                🧹 Limpar
            </a>
        </div>
    </form>

    {{-- 📋 TABELA RESPONSIVA --}}
    <div class="overflow-x-auto">

        {{-- Desktop --}}
        <table class="w-full table-auto border-collapse shadow-sm hidden sm:table text-center">
            <thead class="bg-gray-100 text-sm uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="border px-4 py-2">Activo</th>
                    <th class="border px-4 py-2">Etiqueta</th>
                    <th class="border px-4 py-2">Tipo</th>
                    <th class="border px-4 py-2">Inicio da Manutenção</th>
                    <th class="border px-4 py-2">Data da Conclusão</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Responsável</th>
                    <th class="border px-4 py-2 w-48">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($manutencoes as $m)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="border px-4 py-2">{{ $m->bem->Nome ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $m->bem->Etiqueta ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $m->tipo }}</td>
                        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($m->data_manutencao)->format('d/m/Y') }}</td>
                        <td class="border px-4 py-2">{{ $m->DataConclusao ? \Carbon\Carbon::parse($m->DataConclusao)->format('d/m/Y') : '-' }}</td>
                        <td class="border px-4 py-2">
                            @php
                                $statusColors = [
                                    'Pendente' => 'bg-yellow-100 text-yellow-800',
                                    'Em Andamento' => 'bg-blue-100 text-blue-800',
                                    'Concluída' => 'bg-green-100 text-green-800',
                                    'Cancelada' => 'bg-red-100 text-red-800',
                                ];
                                $colorClass = $statusColors[$m->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $colorClass }}">
                                {{ $m->status }}
                            </span>
                        </td>
                        <td class="border px-4 py-2">{{ $m->responsavel }}</td>
                        <td class="border px-4 py-2 w-48">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('manutencoes.show', $m->id) }}" class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm text-center">Ver</a>
                                <a href="{{ route('manutencoes.edit', $m->id) }}" class="px-3 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 transition text-sm text-center">Editar</a>
                                <form action="{{ route('manutencoes.destroy', $m->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm text-center" onclick="return confirm('Confirma exclusão?')">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-gray-500">
                            Nenhuma manutenção encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Mobile: Cards --}}
        <div class="sm:hidden flex flex-col gap-4">
            @forelse($manutencoes as $m)
                <div class="border rounded-lg p-4 shadow-sm bg-white">
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Bem:</span> {{ $m->bem->Nome ?? '-' }}
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Etiqueta:</span> {{ $m->bem->Etiqueta ?? '-' }}
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Tipo:</span> {{ $m->tipo }}
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Inicio da Manutenção:</span> {{ \Carbon\Carbon::parse($m->data_manutencao)->format('d/m/Y') }}
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Data da Conclusão:</span> {{ $m->DataConclusao ? \Carbon\Carbon::parse($m->DataConclusao)->format('d/m/Y') : '-' }}
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Status:</span>
                        @php
                            $colorClass = $statusColors[$m->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-sm font-medium {{ $colorClass }}">
                            {{ $m->status }}
                        </span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold">Responsável:</span> {{ $m->responsavel }}
                    </div>

                    {{-- Botões Mobile (largura total) --}}
                    <div class="flex flex-col gap-2 mt-3">
                        <a href="{{ route('manutencoes.show', $m->id) }}" class="w-full px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm text-center">Ver</a>
                        <a href="{{ route('manutencoes.edit', $m->id) }}" class="w-full px-3 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 transition text-sm text-center">Editar</a>
                        <form action="{{ route('manutencoes.destroy', $m->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm text-center" onclick="return confirm('Confirma exclusão?')">Excluir</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center py-4 text-gray-500">Nenhuma manutenção encontrada.</p>
            @endforelse
        </div>
    </div>

    {{-- Paginação --}}
    <div class="mt-6">
        {{ $manutencoes->links() }}
    </div>
</div>
@endsection
