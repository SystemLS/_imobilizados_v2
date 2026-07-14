@extends('layouts.app')

@section('title', 'Cadastrar Usuário')
@section('page-title', 'Cadastrar Novo Usuário')

@section('content')
<div class="max-w-lg mx-auto bg-white shadow-lg rounded-xl p-8 mt-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Cadastrar Novo Usuário</h2>

    {{-- Mensagens de erro --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Nome</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="border border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200"
                   placeholder="Digite o nome completo" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="border border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200"
                   placeholder="Digite o email" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Perfil</label>
            <select name="perfil" required
                    class="border border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200 bg-gray-50">
                <option value="">Selecione o perfil</option>
                <option value="administrador" {{ old('perfil')=='administrador' ? 'selected' : '' }}>Administrador</option>
                <option value="gestor" {{ old('perfil')=='gestor' ? 'selected' : '' }}>Gestor Patrimonial</option>
                <option value="tecnico_contabilidade" {{ old('perfil')=='tecnico_contabilidade' ? 'selected' : '' }}>Técnico de Contabilidade</option>
                <option value="tecnico_manutencao" {{ old('perfil')=='tecnico_manutencao' ? 'selected' : '' }}>Técnico de Manutenção</option>
                <option value="tecnico_cadastro" {{ old('perfil')=='tecnico_cadastro' ? 'selected' : '' }}>Técnico de Cadastro</option>
                <option value="padrao" {{ old('perfil')=='padrao' ? 'selected' : '' }}>Padrão</option>
            </select>
        </div>

        {{-- Botões --}}

<div class="flex flex-col sm:flex-row justify-between gap-3 mt-4">
    <a href="{{ route('config.index') }}"
       class="w-full sm:w-[120%] bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 rounded-lg shadow-md text-center transition duration-200">
       Voltar
    </a>

    <button type="submit"
        class="w-full sm:w-[120%] bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition duration-200">
        Cadastrar Usuário
    </button>
</div>

    </form>
</div>
@endsection
