@extends('layouts.app')

@section('title', 'Alterar Fotografia')
@section('page-title', 'Alterar Fotografia')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-xl shadow-lg transform transition duration-500 hover:scale-105">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Alterar Fotografia do Perfil</h2>

    <form action="{{ route('users.fotografia.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Pré-visualização da foto --}}
        <div class="flex justify-center mb-4">
            @php
                $user = auth()->user();
            @endphp
            @if($user->fotografia)
                <img id="preview" src="{{ asset('storage/' . $user->fotografia) }}" alt="Foto do Usuário" class="w-32 h-32 rounded-full object-cover border-2 border-indigo-500">
            @else
                <div id="preview" class="w-32 h-32 rounded-full bg-indigo-500 flex items-center justify-center text-white text-3xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
            @endif
        </div>

        {{-- Input de arquivo --}}
        <div class="mb-6">
            <label class="block font-semibold text-gray-700 mb-2">Escolha uma fotografia (.jpg ou .png)</label>
            <input type="file" name="fotografia" accept=".jpg,.png" required
                   class="w-full text-gray-700 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                   onchange="previewImage(event)">
            <x-input-error :messages="$errors->get('fotografia')" class="mt-2" />
        </div>

        {{-- Botões --}}
        <div class="flex justify-between mt-6">
            <a href="{{ route('painel.controle') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded shadow transition">
                Voltar
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow transition">
                Salvar Fotografia
            </button>
        </div>
    </form>
</div>

{{-- Script para pré-visualizar imagem --}}
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('preview');
        if(output.tagName === 'IMG') {
            output.src = reader.result;
        } else {
            // caso seja o div das iniciais
            output.style.backgroundImage = `url(${reader.result})`;
            output.textContent = '';
            output.style.backgroundSize = 'cover';
            output.style.backgroundPosition = 'center';
        }
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
