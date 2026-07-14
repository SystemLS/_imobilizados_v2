@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 max-w-3xl">

    {{-- Cabeçalho --}}
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Novo Edifício</h1>

    {{-- Mensagens de erro --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dados_mestres.edificios.store') }}" method="POST" class="bg-white p-6 rounded-xl shadow-lg">
        @csrf

        {{-- Nome --}}
        <div class="mb-4">
            <label for="Nome" class="block text-gray-700 font-semibold mb-2">Nome do Edifício</label>
            <input type="text" name="Nome" id="Nome" value="{{ old('Nome') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="Digite o nome do edifício">
        </div>

        {{-- Província --}}
        <div class="mb-6">
            <label for="ProvinciaId" class="block text-gray-700 font-semibold mb-2">Província</label>
            <select name="ProvinciaId" id="ProvinciaId"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Selecione a província</option>
                @foreach($provincias as $provincia)
                    <option value="{{ $provincia->ProvinciaId }}" {{ old('ProvinciaId') == $provincia->ProvinciaId ? 'selected' : '' }}>
                        {{ $provincia->Nome }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Botões --}}
        <div class="flex gap-4">
            <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition">
                Salvar
            </button>
            <a href="{{ route('dados_mestres.edificios.index') }}"
               class="px-6 py-2 bg-gray-400 text-white font-semibold rounded-lg shadow hover:bg-gray-500 transition">
               Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
