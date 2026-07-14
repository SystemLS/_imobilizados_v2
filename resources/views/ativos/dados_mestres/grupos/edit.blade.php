@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Editar Grupo</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dados_mestres.grupos.update', $grupo->GrupoId) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="Nome" class="block text-sm font-medium text-gray-700">Nome do Grupo</label>
            <input type="text" id="Nome" name="Nome" value="{{ old('Nome', $grupo->Nome) }}"
                class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-200"
                required>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('dados_mestres.grupos.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</a>
            <button type="submit"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Atualizar</button>
        </div>
    </form>
</div>
@endsection
