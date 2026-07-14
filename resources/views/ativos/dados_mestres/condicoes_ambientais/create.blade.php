@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 max-w-3xl">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">➕ Nova Condição Ambiental</h1>
        <a href="{{ route('dados_mestres.condicoes_ambientais.index') }}"
           class="px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow hover:bg-gray-600 transition w-full sm:w-auto text-center">
            Voltar
        </a>
    </div>

    {{-- Erros --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg shadow-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <form action="{{ route('dados_mestres.condicoes_ambientais.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="Nome" class="block text-gray-700 font-semibold mb-2">Nome</label>
                <input type="text" id="Nome" name="Nome"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="Ex: Bom, Danificado"
                       value="{{ old('Nome') }}" required>
            </div>


            {{-- Botões --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('dados_mestres.condicoes_ambientais.index') }}"
                   class="px-5 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
