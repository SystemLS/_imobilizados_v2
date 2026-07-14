@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-xl py-10">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Adicionar Novo Piso</h1>

    <form action="{{ route('dados_mestres.pisos.store') }}" method="POST"
          class="bg-white p-6 rounded-lg shadow-lg border border-gray-200">
        @csrf

        <div class="mb-4">
            <label for="EdificioId" class="block font-semibold mb-2">Edifício</label>
            <select id="EdificioId" name="EdificioId"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                <option value="">-- Selecione o edifício --</option>
                @foreach ($edificios as $edificio)
                    <option value="{{ $edificio->EdificioId }}" {{ old('EdificioId') == $edificio->EdificioId ? 'selected' : '' }}>
                        {{ $edificio->Nome }}
                    </option>
                @endforeach
            </select>
            @error('EdificioId')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="Nome" class="block font-semibold mb-2">Nome do Piso</label>
            <input type="text" id="Nome" name="Nome" value="{{ old('Nome') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
            @error('Nome')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('dados_mestres.pisos.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancelar</a>
            <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Salvar</button>
        </div>
    </form>
</div>
@endsection
