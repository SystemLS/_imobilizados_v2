@extends('layouts.app')

@section('title', 'Configurações')
@section('page-title', 'Configurações do Sistema')

@section('content')

{{-- Mensagens de sucesso --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <nav class="flex flex-wrap gap-2 bg-white border border-gray-200 rounded-full shadow-sm p-1">
            <a href="{{ route('config.index') }}"
               class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('config.index') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                Usuários
            </a>
            <a href="{{ route('config.integracao') }}"
               class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('config.integracao') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                Integração
            </a>
        </nav>
        <h3 class="text-lg font-semibold text-gray-700">Usuários cadastrados</h3>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('config.usuarios.export.pdf') }}"
           class="flex items-center gap-1 px-3 sm:px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition text-sm">
            <i data-feather="file-pdf" style="width: 16px; height: 16px;"></i> PDF
        </a>
        <a href="{{ route('config.usuarios.export.excel') }}"
           class="flex items-center gap-1 px-3 sm:px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition text-sm">
            <i data-feather="file" style="width: 16px; height: 16px;"></i> Excel
        </a>
        <a href="{{ route('users.create') }}"
           class="bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-full hover:bg-blue-700 transition duration-200 shadow">
            Cadastrar Novo Usuário
        </a>
    </div>
</div>

{{-- Desktop Table --}}
<div class="hidden sm:block">
    <table class="min-w-full bg-white shadow rounded-xl overflow-hidden border border-gray-100 text-sm sm:text-base">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-3 px-4 text-left text-gray-700 font-semibold">Usuário</th>
                <th class="py-3 px-4 text-left text-gray-700 font-semibold">Email</th>
                <th class="py-3 px-4 text-left text-gray-700 font-semibold">Perfil</th>
                <th class="py-3 px-4 text-right text-gray-700 font-semibold">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $user)
                @php
                    $iniciais = collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->join('');
                    $isSelfAdmin = auth()->user()->id === $user->id && $user->perfil === 'administrador';
                @endphp
                <tr class="border-b hover:bg-gray-50 transition duration-150">
                    <td class="py-3 px-4 flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden border-2 border-gray-300">
                            @if($user->fotografia)
                                <img src="{{ asset('storage/' . $user->fotografia) }}" alt="Foto do Usuário" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper($iniciais) }}
                                </div>
                            @endif
                        </div>
                        <span class="text-gray-700 font-medium">{{ $user->name }}</span>
                    </td>
                    <td class="py-3 px-4 text-gray-600">{{ $user->email }}</td>
                    <td class="py-3 px-4">
                        <form action="{{ route('config.users.updatePerfil', $user) }}" method="POST" class="flex items-center space-x-2">
                            @csrf
                            @method('PATCH')
                            <select name="perfil"
                                    class="border border-gray-300 rounded-full px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-150 shadow-sm {{ $isSelfAdmin ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-gray-50' }}"
                                    {{ $isSelfAdmin ? 'disabled' : '' }}>
                                <option value="administrador" {{ $user->perfil === 'administrador' ? 'selected' : '' }}>Administrador</option>
                                <option value="gestor" {{ $user->perfil === 'gestor' ? 'selected' : '' }}>Gestor Patrimonial</option>
                                <option value="tecnico_contabilidade" {{ $user->perfil === 'tecnico_contabilidade' ? 'selected' : '' }}>Técnico de Contabilidade</option>
                                <option value="tecnico_manutencao" {{ $user->perfil === 'tecnico_manutencao' ? 'selected' : '' }}>Técnico de Manutenção</option>
                                <option value="tecnico_cadastro" {{ $user->perfil === 'tecnico_cadastro' ? 'selected' : '' }}>Técnico de Cadastro</option>
                                <option value="padrao" {{ $user->perfil === 'padrao' ? 'selected' : '' }}>Padrão</option>
                            </select>
                            <button type="submit"
                                    class="bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600 transition duration-200 shadow-sm {{ $isSelfAdmin ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $isSelfAdmin ? 'disabled' : '' }}>
                                Alterar
                            </button>
                        </form>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex justify-end items-center gap-2 flex-nowrap">
                            <a href="{{ route('config.users.edit', $user) }}"
                               class="bg-yellow-500 text-white px-3 py-1 rounded-lg shadow hover:bg-yellow-600 transition whitespace-nowrap {{ $isSelfAdmin ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                               Editar
                            </a>
                            <form action="{{ route('config.users.destroy', $user) }}" method="POST"
                                  onsubmit="return confirm('Deseja realmente deletar este usuário?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-500 text-white px-3 py-1 rounded-lg shadow hover:bg-red-700 transition whitespace-nowrap {{ $isSelfAdmin ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $isSelfAdmin ? 'disabled' : '' }}>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Mobile Cards --}}
<div class="sm:hidden grid grid-cols-1 gap-3">
    @foreach($usuarios as $user)
        @php
            $iniciais = collect(explode(' ', $user->name))->map(fn($n) => substr($n,0,1))->join('');
            $isSelfAdmin = auth()->user()->id === $user->id && $user->perfil === 'administrador';
        @endphp
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 shadow-sm">
            <div class="flex items-center mb-1 space-x-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden border-2 border-gray-300">
                    @if($user->fotografia)
                        <img src="{{ asset('storage/' . $user->fotografia) }}" alt="Foto do Usuário" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper($iniciais) }}
                        </div>
                    @endif
                </div>
                <div>
                    <p class="text-gray-700 font-medium text-sm">{{ $user->name }}</p>
                    <p class="text-gray-600 text-sm truncate">{{ $user->email }}</p>
                </div>
            </div>

            {{-- Perfil --}}
            <form action="{{ route('config.users.updatePerfil', $user) }}" method="POST" class="flex items-center gap-2 mb-2">
                @csrf
                @method('PATCH')
                <select name="perfil"
                        class="border border-gray-300 rounded-full px-2 py-1 text-gray-700 text-sm shadow-sm flex-1 focus:outline-none focus:ring-2 focus:ring-blue-400 transition {{ $isSelfAdmin ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-gray-50' }}"
                        {{ $isSelfAdmin ? 'disabled' : '' }}>
                    <option value="administrador" {{ $user->perfil === 'administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="gestor" {{ $user->perfil === 'gestor' ? 'selected' : '' }}>Gestor Patrimonial</option>
                    <option value="tecnico_contabilidade" {{ $user->perfil === 'tecnico_contabilidade' ? 'selected' : '' }}>Técnico de Contabilidade</option>
                    <option value="tecnico_manutencao" {{ $user->perfil === 'tecnico_manutencao' ? 'selected' : '' }}>Técnico de Manutenção</option>
                    <option value="tecnico_cadastro" {{ $user->perfil === 'tecnico_cadastro' ? 'selected' : '' }}>Técnico de Cadastro</option>
                    <option value="padrao" {{ $user->perfil === 'padrao' ? 'selected' : '' }}>Padrão</option>
                </select>
                <button type="submit"
                        class="bg-green-500 text-white px-3 py-1 rounded-full hover:bg-green-600 transition text-sm flex-shrink-0 {{ $isSelfAdmin ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $isSelfAdmin ? 'disabled' : '' }}>
                    Alterar
                </button>
            </form>

            {{-- Ações --}}
            <div class="flex gap-2">
                <a href="{{ route('config.users.edit', $user) }}"
                   class="w-1/2 py-2 bg-yellow-500 text-white rounded-lg text-center text-sm hover:bg-yellow-600 transition {{ $isSelfAdmin ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                    Editar
                </a>
                <form action="{{ route('config.users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Deseja realmente deletar este usuário?');"
                      class="w-1/2">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-700 transition {{ $isSelfAdmin ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $isSelfAdmin ? 'disabled' : '' }}>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>

@endsection
