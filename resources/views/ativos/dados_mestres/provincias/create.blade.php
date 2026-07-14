@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 max-w-2xl">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Cadastrar Nova Província</h1>
        <a href="{{ route('dados_mestres.provincias.index') }}"
           class="px-5 py-2 bg-gray-400 text-white rounded-lg shadow hover:bg-gray-500 transition text-center">
           Voltar à Lista
        </a>
    </div>

    {{-- Formulário --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <form action="{{ route('dados_mestres.provincias.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="Nome" class="block text-gray-700 font-semibold mb-2">Nome da Província</label>
                <input type="text" name="Nome" id="Nome"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition"
                       placeholder="Ex: Luanda" required>
                @error('Nome')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botões --}}
            <div class="flex justify-end gap-3 flex-wrap">
                <a href="{{ route('dados_mestres.provincias.index') }}"
                   class="px-5 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script para alerta de sucesso e redirecionamento --}}
@if(session('success'))
<script>
    alert('{{ session('success') }}');
    window.location.href = "{{ route('dados_mestres.provincias.index') }}";
</script>
@endif

@endsection
