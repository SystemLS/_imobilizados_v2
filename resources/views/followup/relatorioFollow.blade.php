@extends('layouts.app')

@section('title','Relatórios Follow Up')

@section('content')
<div class="container mx-auto p-4 md:p-6">

    {{-- Cabeçalho --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h1 class="text-2xl font-bold text-gray-700">📊 Relatórios de Follow Up</h1>
        <a href="{{ route('inventario.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            Voltar ao Inventário
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded-xl shadow p-4">

        {{-- Tabela Desktop --}}
        <div class="hidden md:block">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr class="text-center">
                        <th class="p-2 border">Nº</th>
                        <th class="p-2 border">Sala</th>
                        <th class="p-2 border">Piso</th>
                        <th class="p-2 border">Edifício</th>
                        <th class="p-2 border">Província</th>
                        <th class="p-2 border">Responsável</th>
                        <th class="p-2 border">Iniciado Em</th>
                        <th class="p-2 border">Finalizado Em</th>
                        <th class="p-2 border">Ativos Encontrados</th>
                        <th class="p-2 border">Ativos Não Encontrados</th>
                        <th class="p-2 border">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($follows as $i => $fu)
                    <tr class="border-b hover:bg-gray-50 transition text-center">
                        <td class="p-2">{{ $i + 1 }}</td>
                        <td class="p-2">{{ $fu->sala?->Nome ?? '-' }}</td>
                        <td class="p-2">{{ $fu->piso?->Nome ?? '-' }}</td>
                        <td class="p-2">{{ $fu->edificio?->Nome ?? '-' }}</td>
                        <td class="p-2">{{ $fu->provincia?->Nome ?? '-' }}</td>
                        <td class="p-2">{{ $fu->usuario?->name ?? '-' }}</td>
                        <td class="p-2">{{ $fu->iniciado_em ? \Carbon\Carbon::parse($fu->iniciado_em)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="p-2">{{ $fu->finalizado_em ? \Carbon\Carbon::parse($fu->finalizado_em)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="p-2">{{ $fu->ativos_encontrados ?? 0 }}</td>
                        <td class="p-2">{{ $fu->ativos_nao_encontrados ?? 0 }}</td>
                        <td class="p-2 flex justify-center gap-2">
                            <a href="{{ route('followup.export', ['id' => $fu->id, 'tipo' => 'pdf']) }}"
                               class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">PDF</a>
                            <a href="{{ route('followup.export', ['id' => $fu->id, 'tipo' => 'excel']) }}"
                               class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">Excel</a>
                            <a href="{{ route('followup.comparacao', $fu->id) }}"
                               class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tabela Mobile --}}
        <div class="md:hidden flex flex-col gap-4">
            @foreach($follows as $i => $fu)
            <div class="bg-gray-50 rounded-lg shadow p-3">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-semibold">Follow Up #{{ $i + 1 }}</div>
                    <div class="flex gap-1">
                        <a href="{{ route('followup.export', ['id' => $fu->id, 'tipo' => 'pdf']) }}"
                           class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">PDF</a>
                        <a href="{{ route('followup.export', ['id' => $fu->id, 'tipo' => 'excel']) }}"
                           class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Excel</a>
                        <a href="{{ route('followup.comparacao', $fu->id) }}"
                           class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Ver</a>
                    </div>
                </div>
                <div class="text-xs space-y-1">
                    <p><strong>Sala:</strong> {{ $fu->sala?->Nome ?? '-' }}</p>
                    <p><strong>Piso:</strong> {{ $fu->piso?->Nome ?? '-' }}</p>
                    <p><strong>Edifício:</strong> {{ $fu->edificio?->Nome ?? '-' }}</p>
                    <p><strong>Província:</strong> {{ $fu->provincia?->Nome ?? '-' }}</p>
                    <p><strong>Responsável:</strong> {{ $fu->usuario?->name ?? '-' }}</p>
                    <p><strong>Iniciado Em:</strong> {{ $fu->iniciado_em ? \Carbon\Carbon::parse($fu->iniciado_em)->format('d/m/Y H:i') : '-' }}</p>
                    <p><strong>Finalizado Em:</strong> {{ $fu->finalizado_em ? \Carbon\Carbon::parse($fu->finalizado_em)->format('d/m/Y H:i') : '-' }}</p>
                    <p><strong>Ativos Encontrados:</strong> {{ $fu->ativos_encontrados ?? 0 }}</p>
                    <p><strong>Ativos Não Encontrados:</strong> {{ $fu->ativos_nao_encontrados ?? 0 }}</p>
                </div>
            </div>
            @endforeach
        </div>

        @if($follows->isEmpty())
            <p class="text-center text-gray-500 mt-4">Nenhum follow up registrado ainda.</p>
        @endif
    </div>

    <div class="mt-4">
        {{ $follows->links() }}
    </div>

</div>
@endsection
