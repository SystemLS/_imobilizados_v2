@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 max-w-5xl">
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
        @php
            $bem = $reavaliacao->bem ?? null;
            $dataAquisicao = $reavaliacao->data_aquisicao ?? ($bem?->data_aquisicao ?? $bem?->created_at);
        @endphp

        <h1 class="text-3xl font-extrabold text-gray-800 mb-6">Detalhes da Reavaliacao</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-xs uppercase text-gray-500 font-semibold">Activo</p>
                <p class="text-lg font-semibold text-gray-800 mt-1">{{ $bem?->Nome ?? 'N/D' }}</p>
                <p class="text-sm text-gray-600 mt-1">Etiqueta: <span class="font-medium">{{ $bem?->Etiqueta ?? '-' }}</span></p>
                <p class="text-sm text-gray-600 mt-1">
                    Data de Aquisicao: {{ $dataAquisicao ? \Carbon\Carbon::parse($dataAquisicao)->format('d/m/Y') : '-' }}
                </p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-xs uppercase text-gray-500 font-semibold">Taxa de Depreciacao</p>
                <p class="text-lg font-semibold text-blue-700 mt-1">
                    {{ number_format($reavaliacao->taxa_depreciacao ?? 0, 2, ',', '.') }}%
                </p>
                <p class="text-sm text-gray-600 mt-1">Vida util: {{ $reavaliacao->vida_util ?? '-' }} anos</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-xs uppercase text-gray-500 font-semibold">Valor Inicial</p>
                <p class="text-lg font-semibold text-gray-800 mt-1">
                    {{ number_format($reavaliacao->valor_inicial ?? 0, 2, ',', '.') }} Kz
                </p>
            </div>

            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-100 shadow-sm">
                <p class="text-xs uppercase text-yellow-700 font-semibold">VLC</p>
                <p class="text-lg font-semibold text-yellow-800 mt-1">
                    {{ number_format($reavaliacao->vlc ?? 0, 2, ',', '.') }} Kz
                </p>
            </div>

            <div class="p-4 bg-green-50 rounded-lg border border-green-100 shadow-sm">
                <p class="text-xs uppercase text-green-700 font-semibold">Valor Atualizado</p>
                <p class="text-lg font-extrabold text-green-700 mt-1">
                    {{ number_format($reavaliacao->valor_atualizado ?? 0, 2, ',', '.') }} Kz
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 shadow-sm">
                <p class="text-xs uppercase text-blue-700 font-semibold">Nova Depreciacao Anual</p>
                <p class="text-lg font-semibold text-blue-800 mt-1">
                    {{ number_format($reavaliacao->nova_depreciacao_anual ?? 0, 2, ',', '.') }} Kz/ano
                </p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-xs uppercase text-gray-500 font-semibold">Valor Residual</p>
                <p class="text-lg font-semibold text-gray-800 mt-1">
                    {{ number_format($reavaliacao->valor_residual ?? 0, 2, ',', '.') }} Kz
                </p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-xs uppercase text-gray-500 font-semibold">Data da Reavaliacao</p>
                <p class="text-lg font-semibold text-gray-800 mt-1">
                    {{ $reavaliacao->data_reavaliacao ? \Carbon\Carbon::parse($reavaliacao->data_reavaliacao)->format('d/m/Y') : '-' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-xs uppercase text-gray-500 font-semibold">Metodo</p>
                <p class="text-lg font-semibold text-gray-800 mt-1">{{ $reavaliacao->metodo ?? '-' }}</p>
            </div>
        </div>

        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 shadow-sm mb-6">
            <p class="text-xs uppercase text-gray-500 font-semibold">Observacoes</p>
            <p class="text-gray-700 mt-1">{{ $reavaliacao->observacoes ?? '-' }}</p>
        </div>

        <div class="flex justify-between items-center flex-wrap mt-6">
            <a href="{{ route('reavaliacoes.index') }}"
               class="px-6 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition-all duration-300">
               Voltar
            </a>

            @if($reavaliacao && in_array(auth()->user()->perfil, ['administrador','gestor','tecnico_contabilidade']))
                <a href="{{ route('reavaliacoes.edit', ['id' => $reavaliacao->id]) }}"
                   class="px-6 py-2 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition-all duration-300">
                   Editar
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
