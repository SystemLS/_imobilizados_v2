@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
            Detalhes do Ativo: {{ $bem->Nome }}
        </h1>
    </div>

    {{-- Grid principal responsivo --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2">

        {{-- Dados de Localização --}}
        <div class="bg-gray-800 text-white rounded-xl shadow-lg p-6 transform transition duration-300 hover:scale-105">
            <h2 class="text-xl font-semibold mb-3">Dados de Localização</h2>
            <p><strong>Nome:</strong> {{ $bem->Nome }}</p>
            <p><strong>Categoria:</strong> {{ $bem->subcategoria->Nome ?? '-' }}</p>
            <p><strong>Localização:</strong> {{ $bem->sala->Nome ?? '-' }}</p>
            <p><strong>Província:</strong> {{ $bem->sala->piso->edificio->provincia->Nome ?? '-' }}</p>
            <p><strong>Edifício:</strong> {{ $bem->sala->piso->edificio->Nome ?? '-' }}</p>
            <p><strong>Piso:</strong> {{ $bem->sala->piso->Nome ?? '-' }}</p>
            <p><strong>Data de Aquisição:</strong> {{ $bem->data_aquisicao ?? 'Sem Registro' }}</p>
        </div>

        {{-- Dados de Operação --}}
        <div class="bg-gray-700 text-white rounded-xl shadow-lg p-6 transform transition duration-300 hover:scale-105">
            <h2 class="text-xl font-semibold mb-3">Dados de Operação</h2>
            <p><strong>Estado:</strong> {{ $bem->estadoConservacao->Nome ?? '-' }}</p>
            <p><strong>Condição Ambiental:</strong> {{ $bem->condicaoAmbiental->Nome ?? '-' }}</p>
        </div>

        {{-- Dados de Especificações --}}
        <div class="bg-gray-700 text-white rounded-xl shadow-lg p-6 transform transition duration-300 hover:scale-105">
            <h2 class="text-xl font-semibold mb-3">Especificações</h2>
            <p><strong>Marca/Modelo:</strong> {{ $bem->Marca }} / {{ $bem->Modelo }}</p>
            <p><strong>Tipo/Nº Série:</strong> {{ $bem->TipoNumeroSerie }} / {{ $bem->NumeroSerieManual }}</p>
        </div>

        {{-- Dados Financeiros --}}
        <div class="bg-gray-700 text-white rounded-xl shadow-lg p-6 transform transition duration-300 hover:scale-105">
            <h2 class="text-xl font-semibold mb-3">Dados Financeiros</h2>
            <p><strong>Preço de Aquisição:</strong> {{ number_format($bem->preco_aquisicao, 2, ',', '.') }} Kz</p>
            @foreach($bem->reavaliacoes as $reavaliacao)
                <p><strong>Valor Contabilistico:</strong> {{ number_format($reavaliacao->valor_liquido_contabilistico, 2, ',', '.') }} Kz</p>
                <p><strong>Valor Reavaliado (Valor Justo):</strong> {{ number_format($reavaliacao->valor_justo, 2, ',', '.') }} Kz</p>
            @endforeach
        </div>

        {{-- Fotos --}}
        <div class="col-span-1 md:col-span-2 bg-white rounded-xl shadow-lg p-6 transform transition duration-300 hover:scale-105">
            <h2 class="text-xl font-semibold mb-3 text-gray-800">Fotos Principais</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach(['Foto1','Foto2','Foto3'] as $foto)
                    @if($bem->$foto)
                        <img src="{{ asset('storage/' . $bem->$foto) }}" alt="Foto do ativo" class="rounded-lg shadow-sm object-cover h-48 w-full">
                    @endif
                @endforeach
                @if(!($bem->Foto1 || $bem->Foto2 || $bem->Foto3))
                    <p class="text-gray-500 col-span-3">Nenhuma foto disponível.</p>
                @endif
            </div>
        </div>

        {{-- Materiais --}}
        <div class="bg-white rounded-xl shadow-lg p-6 transform transition duration-300 hover:scale-105">
            <h2 class="text-xl font-semibold mb-3 text-gray-800">Materiais</h2>
            @if($bem->materiais->count() > 0)
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($bem->materiais as $material)
                        <li>{{ $material->Nome }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">Nenhum material associado.</p>
            @endif
        </div>

        {{-- Manutenções --}}
        <div class="bg-white rounded-xl shadow-lg p-6 transform transition duration-300 hover:scale-105">
            <h2 class="text-xl font-semibold mb-3 text-gray-800">Manutenções</h2>
            @if($bem->manutencoes->count() > 0)
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($bem->manutencoes as $manutencao)
                        <li>{{ $manutencao->descricao }} - {{ $manutencao->data_manutencao ? \Carbon\Carbon::parse($manutencao->data_manutencao)->format('d/m/Y') : '-' }} <br>Responsável: {{ $manutencao->responsavel ?? '-' }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">Nenhuma manutenção registrada.</p>
            @endif
        </div>

        {{-- Reavaliações --}}
        <div class="bg-white rounded-xl shadow-lg p-6 transform transition duration-300 hover:scale-105">
            <h2 class="text-xl font-semibold mb-3 text-gray-800">Reavaliações</h2>
            @if($bem->reavaliacoes->count() > 0)
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($bem->reavaliacoes as $reavaliacao)
                        <li>
                            Depreciação Anual: {{ number_format($reavaliacao->nova_depreciacao ?? 0, 2, ',', '.') }} Kz/Ano <br>
                            Data da Reavaliação: {{ $reavaliacao->data_reavaliacao ? \Carbon\Carbon::parse($reavaliacao->data_reavaliacao)->format('d/m/Y') : '-' }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">Nenhuma reavaliação registrada.</p>
            @endif
        </div>

        {{-- Descrição Completa --}}
        <div class="col-span-1 md:col-span-2 bg-white rounded-xl shadow-lg p-6 transform transition duration-300 hover:scale-105">
            <h2 class="text-xl font-semibold mb-3 text-gray-800">Descrição Completa</h2>
            <p class="text-gray-700 whitespace-pre-line">{{ $bem->Descricao ?? '-' }}</p>
        </div>

    </div>

    {{-- Botões --}}
    <div class="mt-6 flex flex-col sm:flex-row sm:justify-between gap-4">
        <a href="{{ route('ativos.index') }}"
           class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition text-center">
           Voltar
        </a>

        @auth
            @if(in_array(auth()->user()->perfil, ['administrador', 'gestor', 'tecnico_cadastro', 'tecnico_contabilidade']))
                <a href="{{ route('ativos.edit', ['bem' => $bem->BemId]) }}"
                   class="inline-block px-6 py-2 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition text-center">
                   ✏️ Editar
                </a>
            @endif
        @endauth
    </div>

</div>
@endsection
