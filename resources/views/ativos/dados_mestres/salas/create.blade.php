@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        Cadastrar Nova Sala
    </h2>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <form action="{{ route('dados_mestres.salas.store') }}" method="POST" id="salaForm">
        @csrf

        {{-- Província --}}
        <div class="mb-4">
            <label for="provincia" class="block text-sm font-medium text-gray-700">Província</label>
            <select id="provincia" name="provincia" class="form-select w-full rounded-lg border-gray-300" required>
                <option value="">Selecione uma província</option>
                @foreach ($provincias as $prov)
                    <option value="{{ $prov->ProvinciaId }}">{{ $prov->Nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Edifício --}}
        <div class="mb-4">
            <label for="edificio" class="block text-sm font-medium text-gray-700">Edifício</label>
            <select id="edificio" name="edificio" class="form-select w-full rounded-lg border-gray-300" required disabled>
                <option value="">Selecione o edifício</option>
            </select>
        </div>

        {{-- Piso --}}
        <div class="mb-4">
            <label for="PisoId" class="block text-sm font-medium text-gray-700">Piso</label>
            <select id="PisoId" name="PisoId" class="form-select w-full rounded-lg border-gray-300" required disabled>
                <option value="">Selecione o piso</option>
            </select>
        </div>

        {{-- Nome da Sala --}}
        <div class="mb-6">
            <label for="Nome" class="block text-sm font-medium text-gray-700">Nome da Sala</label>
            <input type="text" id="Nome" name="Nome"
                class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-200"
                required placeholder="Digite o nome ou número da sala">
            <div id="alertaDuplicado" class="mt-2 text-red-600 font-semibold hidden">
                ⚠️ Já existe uma sala com este nome neste piso.
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('dados_mestres.salas.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</a>
            <button type="submit"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Salvar</button>
        </div>
    </form>
</div>

{{-- Script Dinâmico --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinciaSelect = document.getElementById('provincia');
    const edificioSelect = document.getElementById('edificio');
    const pisoSelect = document.getElementById('PisoId');
    const nomeInput = document.getElementById('Nome');
    const alertaDuplicado = document.getElementById('alertaDuplicado');
    const submitBtn = document.querySelector('button[type="submit"]');

    // Atualizar Edifícios
    provinciaSelect.addEventListener('change', async function() {
        edificioSelect.innerHTML = '<option value="">Carregando...</option>';
        pisoSelect.innerHTML = '<option value="">Selecione o piso</option>';
        pisoSelect.disabled = true;

        if (this.value) {
            edificioSelect.disabled = false;
            const res = await fetch(`/api/edificios/${this.value}`);
            const edificios = await res.json();
            edificioSelect.innerHTML = '<option value="">Selecione o edifício</option>';
            edificios.forEach(ed => {
                edificioSelect.innerHTML += `<option value="${ed.EdificioId}">${ed.Nome}</option>`;
            });
        } else {
            edificioSelect.disabled = true;
        }
    });

    // Atualizar Pisos
    edificioSelect.addEventListener('change', async function() {
        pisoSelect.innerHTML = '<option value="">Carregando...</option>';

        if (this.value) {
            pisoSelect.disabled = false;
            const res = await fetch(`/api/pisos/${this.value}`);
            const pisos = await res.json();
            pisoSelect.innerHTML = '<option value="">Selecione o piso</option>';
            pisos.forEach(p => {
                pisoSelect.innerHTML += `<option value="${p.PisoId}">${p.Nome}</option>`;
            });
        } else {
            pisoSelect.disabled = true;
        }
    });

    // Verificar duplicado
    nomeInput.addEventListener('blur', async function() {
        const nome = nomeInput.value.trim();
        const pisoId = pisoSelect.value;
        if (!nome || !pisoId) return;

        try {
            const res = await fetch(`/salas/verificar-duplicada?Nome=${encodeURIComponent(nome)}&PisoId=${pisoId}`);
            const data = await res.json();
            if (data.existe) {
                alertaDuplicado.classList.remove('hidden');
                submitBtn.disabled = true;
            } else {
                alertaDuplicado.classList.add('hidden');
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error(error);
            alertaDuplicado.classList.add('hidden');
            submitBtn.disabled = false;
        }
    });
});
</script>
@endsection
