@extends('layouts.app')

@section('page-title', 'Detalhes da Manutenção')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-gray-100">
    <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-6 border-b pb-3 flex items-center gap-2">
        <i class="fas fa-tools text-blue-500"></i> Detalhes da Manutenção
    </h1>

    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        {{-- Nome do Bem --}}
        <div class="transition-all hover:scale-[1.02]">
            <label class="text-sm sm:text-base font-semibold text-gray-600">Bem:</label>
            <p class="bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 shadow-inner text-gray-800 font-medium break-words">
                {{ $manutencao->bem->Nome ?? '-' }}
            </p>
        </div>

        {{-- Etiqueta --}}
        <div class="transition-all hover:scale-[1.02]">
            <label class="text-sm sm:text-base font-semibold text-gray-600">Etiqueta:</label>
            <p class="bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 shadow-inner text-gray-800 font-medium break-words">
                {{ $manutencao->bem->Etiqueta ?? '-' }}
            </p>
        </div>

        {{-- Tipo de Manutenção --}}
        <div class="transition-all hover:scale-[1.02]">
            <label class="text-sm sm:text-base font-semibold text-gray-600">Tipo:</label>
            <p class="bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 shadow-inner text-gray-800 font-medium">
                {{ $manutencao->tipo }}
            </p>
        </div>

        {{-- Descrição --}}
        <div class="md:col-span-2 transition-all hover:scale-[1.01]">
            <label class="text-sm sm:text-base font-semibold text-gray-600">Descrição:</label>
            <p class="bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 shadow-inner text-gray-700 leading-relaxed break-words">
                {{ $manutencao->descricao }}
            </p>
        </div>

        {{-- Data da Manutenção --}}
        <div class="transition-all hover:scale-[1.02]">
            <label class="text-sm sm:text-base font-semibold text-gray-600">Data da Manutenção:</label>
            <p class="bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 shadow-inner text-gray-800 font-medium">
                {{ \Carbon\Carbon::parse($manutencao->data_manutencao)->format('d/m/Y') }}
            </p>
        </div>

        {{-- Data de Conclusão --}}
        <div class="transition-all hover:scale-[1.02]">
            <label class="text-sm sm:text-base font-semibold text-gray-600">Data de Conclusão:</label>
            <p class="bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 shadow-inner text-gray-800 font-medium">
                {{ $manutencao->DataConclusao ? \Carbon\Carbon::parse($manutencao->DataConclusao)->format('d/m/Y') : '-' }}
            </p>
        </div>

        {{-- Status --}}
        <div class="transition-all hover:scale-[1.02]">
            <label class="text-sm sm:text-base font-semibold text-gray-600">Status:</label>
            @php
                $statusColors = [
                    'Pendente' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Em Andamento' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'Concluída' => 'bg-green-100 text-green-800 border-green-300',
                    'Cancelada' => 'bg-red-100 text-red-800 border-red-300',
                ];
                $colorClass = $statusColors[$manutencao->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            @endphp
            <p class="bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 shadow-inner">
                <span class="px-3 py-1 rounded-full text-sm sm:text-base font-semibold border {{ $colorClass }}">
                    {{ $manutencao->status }}
                </span>
            </p>
        </div>

        {{-- Responsável --}}
        <div class="transition-all hover:scale-[1.02]">
            <label class="text-sm sm:text-base font-semibold text-gray-600">Responsável:</label>
            <p class="bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 shadow-inner text-gray-800 font-medium break-words">
                {{ $manutencao->responsavel }}
            </p>
        </div>
    </div>

    {{-- Botões responsivos --}}
    <div class="flex flex-col sm:flex-row justify-end mt-6 sm:space-x-3 space-y-2 sm:space-y-0">
        <a href="{{ route('manutencoes.index') }}"
           class="bg-gray-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-gray-700 shadow-md transition text-center w-full sm:w-auto">
            Voltar
        </a>

        <a href="{{ route('manutencoes.edit', $manutencao->id) }}"
           class="bg-yellow-500 text-white px-5 py-2 rounded-xl font-medium hover:bg-yellow-600 shadow-md transition text-center w-full sm:w-auto">
            Editar
        </a>

        <form action="{{ route('manutencoes.destroy', $manutencao->id) }}"
              method="POST"
              onsubmit="return confirm('Deseja realmente excluir esta manutenção?');"
              class="w-full sm:w-auto">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="bg-red-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-red-700 shadow-md transition w-full sm:w-auto">
                Excluir
            </button>
        </form>
    </div>
</div>
@endsection
