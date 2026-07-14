@extends('layouts.app')

@section('title', 'Editar Usuário')
@section('page-title', 'Editar Usuário')

@section('content')
<div class="max-w-2xl mx-auto mt-6 mb-6">
    <nav class="flex flex-wrap gap-2 bg-white border border-gray-200 rounded-full shadow-sm p-1 mb-4">
        <a href="{{ route('config.index') }}"
           class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('config.index') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
            Usuários
        </a>
        <a href="{{ route('config.integracao') }}"
           class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('config.integracao') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
            Integração
        </a>
    </nav>
</div>

<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow">
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Editar Usuário</h3>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('config.users.update', $user) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label class="block font-semibold text-gray-700 mb-1">Nome</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-4">
            <label class="block font-semibold text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-4">
            <label class="block font-semibold text-gray-700 mb-1">Perfil</label>
            <select name="perfil"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="administrador" {{ $user->perfil === 'administrador' ? 'selected' : '' }}>Administrador</option>
                <option value="gestor" {{ $user->perfil === 'gestor' ? 'selected' : '' }}>Gestor Patrimonial</option>
                <option value="tecnico_contabilidade" {{ $user->perfil === 'tecnico_contabilidade' ? 'selected' : '' }}>Técnico de Contabilidade</option>
                <option value="tecnico_manutencao" {{ $user->perfil === 'tecnico_manutencao' ? 'selected' : '' }}>Técnico de Manutenção</option>
                <option value="tecnico_cadastro" {{ $user->perfil === 'tecnico_cadastro' ? 'selected' : '' }}>Técnico de Cadastro</option>
                <option value="padrao" {{ $user->perfil === 'padrao' ? 'selected' : '' }}>Padrão</option>
            </select>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('config.index') }}"
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-full hover:bg-gray-400 transition duration-200 shadow-sm">
               Voltar
            </a>

            <button type="submit"
                    class="bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600 transition duration-200 shadow-sm">
                Atualizar Usuário
            </button>
        </div>
    </form>
</div>
@endsection
