@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 max-w-3xl">

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Categoria</h1>

        <form action="{{ route('dados_mestres.categorias.update', $categoria->CategoriaId) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Grupo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Grupo <span class="text-red-500">*</span></label>
                <select name="GrupoId" class="w-full border-gray-300 rounded-lg p-2 text-gray-700" required>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->GrupoId }}" {{ $categoria->GrupoId == $grupo->GrupoId ? 'selected' : '' }}>
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
                <input type="text" name="Nome" class="w-full border-gray-300 rounded-lg p-2 text-gray-700" value="{{ old('Nome', $categoria->Nome) }}" required>
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
                        class="px-5 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow hover:bg-yellow-600 transition">
                    Atualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
