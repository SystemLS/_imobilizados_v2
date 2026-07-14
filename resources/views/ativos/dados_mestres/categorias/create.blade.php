@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 max-w-3xl">

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Adicionar Nova Categoria</h1>

        <form action="{{ route('dados_mestres.categorias.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Grupo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Grupo <span class="text-red-500">*</span></label>
                <select name="GrupoId" class="w-full border-gray-300 rounded-lg p-2 text-gray-700" required>
                    <option value="">Selecione um grupo</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->GrupoId }}" {{ old('GrupoId') == $grupo->GrupoId ? 'selected' : '' }}>
                            {{ $grupo->Nome }}
                        </option>
                    @endforeach
                </select>
                @error('GrupoId')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nome --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Categoria <span class="text-red-500">*</span></label>
                <input type="text" name="Nome" class="w-full border-gray-300 rounded-lg p-2 text-gray-700" value="{{ old('Nome') }}" required>
                @error('Nome')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botões --}}
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('dados_mestres.categorias.index') }}"
                   class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg shadow hover:bg-gray-300 transition">
                   Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
