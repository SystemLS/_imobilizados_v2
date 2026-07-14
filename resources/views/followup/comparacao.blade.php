@extends('layouts.app')

@section('title','Follow Up — Comparação de Ativos')

@section('content')
<div class="container mx-auto p-4 md:p-6">

    {{-- ================= TÍTULO ================= --}}
    <h1 class="text-2xl font-bold mb-6 text-center md:text-left">
        Comparação Follow Up #{{ $followup->id }}
    </h1>

    {{-- ================= INFORMAÇÕES GERAIS ================= --}}
    <div class="mb-6 flex flex-col md:flex-row md:justify-between gap-4">

        {{-- Dados principais --}}
        <div class="space-y-1 text-sm">
            <p><strong>Responsável:</strong> {{ $followup->usuario->name ?? '-' }}</p>
            <p>
                <strong>Data:</strong>
                {{ optional($followup->finalizado_em ?? $followup->iniciado_em)->format('d/m/Y H:i') }}
            </p>
            <p><strong>Status:</strong> {{ ucfirst($followup->status) }}</p>
            <p><strong>Ativos Encontrados:</strong> {{ $followup->ativos_encontrados }}</p>
            <p><strong>Ativos Não Encontrados:</strong> {{ $followup->ativos_nao_encontrados }}</p>
        </div>

        {{-- Informações gerais e botões --}}
        <div class="mb-6 flex flex-col md:flex-row md:justify-between gap-4 items-start md:items-center">

            {{-- Botões responsivos --}}
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <a href="{{ route('followup.export',['id'=>$followup->id,'tipo'=>'pdf']) }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition w-full sm:w-auto text-center">Exportar PDF</a>
                <a href="{{ route('followup.export',['id'=>$followup->id,'tipo'=>'excel']) }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition w-full sm:w-auto text-center">Exportar Excel</a>
                <a href="{{ route('inventario.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition w-full sm:w-auto text-center">Voltar ao Inventário</a>
            </div>
        </div>
    </div>

    {{-- ================= LOCALIZAÇÃO ================= --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2">Localização do Follow Up</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
            <p><strong>Sala:</strong> {{ $followup->sala->Nome ?? '-' }}</p>
            <p><strong>Piso:</strong> {{ $followup->piso->Nome ?? '-' }}</p>
            <p><strong>Edifício:</strong> {{ $followup->edificio->Nome ?? '-' }}</p>
            <p><strong>Província:</strong> {{ $followup->provincia->Nome ?? '-' }}</p>
        </div>
    </div>

    {{-- ================= ITENS CONFERIDOS ================= --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6 overflow-x-auto">
        <h2 class="text-lg font-semibold mb-3">Itens Conferidos</h2>

        {{-- Tabela Desktop --}}
        <table class="hidden md:table min-w-full text-sm border-collapse">
            <thead class="bg-gray-100">
                <tr class="text-center">
                    <th class="border p-2">Etiqueta</th>
                    <th class="border p-2">Nome</th>
                    <th class="border p-2">Estado</th>
                    <th class="border p-2">Conferido</th>
                </tr>
            </thead>
            <tbody>
                @forelse($followup->itens as $item)
                    <tr class="text-center hover:bg-gray-50">
                        <td class="border p-2">{{ $item->etiqueta }}</td>
                        <td class="border p-2">{{ $item->nome }}</td>
                        <td class="border p-2">{{ $item->estado }}</td>
                        <td class="border p-2">
                            <input type="checkbox" disabled {{ $item->presente ? 'checked' : '' }}>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-red-600">
                            Nenhum item encontrado neste Follow Up.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Cards Mobile --}}
        <div class="md:hidden space-y-3">
            @foreach($followup->itens as $item)
                <div class="border rounded-lg p-3 shadow-sm text-sm">
                    <p><strong>{{ $item->nome }}</strong></p>
                    <p>Etiqueta: {{ $item->etiqueta }}</p>
                    <p>Estado: {{ $item->estado }}</p>
                    <p>Conferido: {{ $item->presente ? 'Sim' : 'Não' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ================= ATIVOS AUSENTES ================= --}}
    @php
        $ativosAusentes = $followup->itens->where('presente', 0);
    @endphp

    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <h2 class="text-lg font-semibold mb-3">Ativos Ausentes</h2>

        @if($ativosAusentes->isEmpty())
            <p class="text-green-600 font-semibold text-center">
                Todos os ativos foram encontrados.
            </p>
        @else
            <table class="hidden md:table min-w-full text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr class="text-center">
                        <th class="border p-2">Etiqueta</th>
                        <th class="border p-2">Nome</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ativosAusentes as $item)
                        <tr class="text-center hover:bg-gray-50">
                            <td class="border p-2">{{ $item->etiqueta }}</td>
                            <td class="border p-2">{{ $item->nome }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Mobile --}}
            <div class="md:hidden space-y-3">
                @foreach($ativosAusentes as $item)
                    <div class="border rounded-lg p-3 shadow-sm text-sm">
                        <p><strong>{{ $item->nome }}</strong></p>
                        <p>Etiqueta: {{ $item->etiqueta }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ================= OBSERVAÇÕES GERAIS ================= --}}
    @if($followup->observacoes)
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="text-lg font-semibold mb-3">Observações Gerais do Follow Up</h2>

        <table class="min-w-full text-sm border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2 text-left">Descrição</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border p-3 text-gray-700">
                        {{ $followup->observacoes }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

</div>

<style>
    input[type="checkbox"]:disabled {
        cursor: not-allowed;
    }
</style>
@endsection
