@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 max-w-2xl">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Editar Província</h1>
        <a href="{{ route('dados_mestres.provincias.index') }}"
           class="px-5 py-2 bg-gray-400 text-white rounded-lg shadow hover:bg-gray-500 transition text-center">
           Voltar à Lista
        </a>
    </div>

    {{-- Mensagens de Sucesso / Erro --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg border border-green-300">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg border border-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulário de Edição --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <form action="{{ route('dados_mestres.provincias.update', $provincia->ProvinciaId) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="Nome" class="block text-gray-700 font-semibold mb-2">Nome da Província</label>
                <input type="text" name="Nome" id="Nome" value="{{ $provincia->Nome }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition"
                       placeholder="Ex: Luanda" required>
                @error('Nome')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 flex-wrap">
                <a href="{{ route('dados_mestres.provincias.index') }}"
                   class="px-5 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                    Atualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
