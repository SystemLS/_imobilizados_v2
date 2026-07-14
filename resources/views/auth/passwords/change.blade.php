@extends('layouts.app')

@section('title', 'Alterar Senha')
@section('page-title', 'Alteração de Senha')

@section('content')
<div class="max-w-md mx-auto mt-16">
    <div class="bg-white rounded-3xl shadow-2xl p-8 transform transition duration-500 hover:scale-105 hover:shadow-3xl border-t-8 border-blue-500">

        {{-- Título --}}
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Alterar Senha</h2>

        {{-- Mensagens de erro --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-800 px-5 py-4 rounded-lg mb-4 animate-fade-in shadow-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Mensagem de sucesso --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-300 text-green-800 px-5 py-4 rounded-lg mb-4 animate-fade-in shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Formulário --}}
        <form action="{{ route('senha.update') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block mb-2 font-semibold text-gray-700">Nova Senha</label>
                <input type="password" name="password" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:outline-none shadow-sm" placeholder="Digite sua nova senha" required>
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-700">Confirmar Senha</label>
                <input type="password" name="password_confirmation" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:outline-none shadow-sm" placeholder="Repita sua nova senha" required>
            </div>

            {{-- Botões --}}
            <div class="flex flex-col sm:flex-row justify-between gap-3 mt-4">
                <a href="{{ route('painel.controle') }}"
                   class="w-full sm:w-[120%] bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 rounded-lg shadow-md text-center transition duration-200">
                   Voltar
                </a>

                <button type="submit"
                        class="w-full sm:w-[120%] py-3 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold hover:from-blue-600 hover:to-indigo-700 transition transform hover:scale-105 shadow-lg">
                    Alterar Senha
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Animação de fade --}}
<style>
    @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
    .animate-fade-in { animation: fade-in 0.7s ease-in-out; }
</style>
@endsection
