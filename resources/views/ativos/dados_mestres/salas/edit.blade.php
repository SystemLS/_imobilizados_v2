@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-2xl shadow-md mt-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">
        ✏️ Editar Sala
    </h2>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('dados_mestres.salas.update', $sala->SalaId) }}" method="POST" id="salaForm">
        @csrf
        @method('PUT')

        {{-- Província --}}
        <div class="mb-4">
            <label for="provincia" class="block text-sm font-medium text-gray-700">Província</label>
            <select id="provincia" name="ProvinciaId"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-200 bg-gray-50 text-gray-800"
                    required>
                <option value="">Selecione uma província</option>
                @foreach ($provincias as $provincia)
                    <option value="{{ $provincia->ProvinciaId }}"
                        {{ $sala->piso->edificio->provincia->ProvinciaId == $provincia->ProvinciaId ? 'selected' : '' }}>
                        {{ $provincia->Nome }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Edifício --}}
        <div class="mb-4">
            <label for="edificio" class="block text-sm font-medium text-gray-700">Edifício</label>
            <select id="edificio" name="EdificioId"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-200 bg-gray-50 text-gray-800"
                    required>
                <option value="{{ $sala->piso->edificio->EdificioId }}">
                    {{ $sala->piso->edificio->Nome }}
                </option>
            </select>
        </div>

        {{-- Piso --}}
        <div class="mb-4">
            <label for="piso" class="block text-sm font-medium text-gray-700">Piso</label>
            <select id="piso" name="PisoId"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-200 bg-gray-50 text-gray-800"
                    required>
                <option value="{{ $sala->piso->PisoId }}">{{ $sala->piso->Nome }}</option>
            </select>
        </div>

        {{-- Sala --}}
        <div class="mb-6">
            <label for="Nome" class="block text-sm font-medium text-gray-700">Nome da Sala</label>
            <input type="text"
                   id="Nome"
                   name="Nome"
                   value="{{ old('Nome', $sala->Nome) }}"
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-200 bg-gray-50 text-gray-800"
                   placeholder="Digite o novo nome da sala"
                   required>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('dados_mestres.salas.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
               Cancelar
            </a>
            <button type="submit"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
               Atualizar
            </button>
        </div>
    </form>
</div>

{{-- Script de dependência em cascata --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const provinciaSelect = document.getElementById('provincia');
    const edificioSelect = document.getElementById('edificio');
    const pisoSelect = document.getElementById('piso');

    // Ao mudar a província → carregar edifícios
    provinciaSelect.addEventListener('change', function() {
        const provinciaId = this.value;
        edificioSelect.innerHTML = '<option value="">Carregando edifícios...</option>';
        pisoSelect.innerHTML = '<option value="">Selecione um edifício primeiro</option>';

        if (provinciaId) {
            fetch(`/api/edificios/${provinciaId}`)
                .then(res => res.json())
                .then(data => {
                    edificioSelect.innerHTML = '<option value="">Selecione um edifício</option>';
                    data.forEach(ed => {
                        edificioSelect.innerHTML += `<option value="${ed.EdificioId}">${ed.Nome}</option>`;
                    });
                });
        } else {
            edificioSelect.innerHTML = '<option value="">Selecione uma província</option>';
        }
    });

    // Ao mudar o edifício → carregar pisos
    edificioSelect.addEventListener('change', function() {
        const edificioId = this.value;
        pisoSelect.innerHTML = '<option value="">Carregando pisos...</option>';

        if (edificioId) {
            fetch(`/api/pisos/${edificioId}`)
                .then(res => res.json())
                .then(data => {
                    pisoSelect.innerHTML = '<option value="">Selecione um piso</option>';
                    data.forEach(p => {
                        pisoSelect.innerHTML += `<option value="${p.PisoId}">${p.Nome}</option>`;
                    });
                });
        } else {
            pisoSelect.innerHTML = '<option value="">Selecione um edifício</option>';
        }
    });
});
</script>
@endsection
