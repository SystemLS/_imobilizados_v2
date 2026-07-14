@extends('layouts.app')

@section('title', 'Painel de Usuário')
@section('page-title', 'Painel de Usuario')

@section('content')
@php
    // Mapear perfis
    $perfisTraduzidos = [
        'administrador' => ['nome' => 'Administrador', 'cor' => 'bg-red-500', 'icone' => 'shield'],
        'gestor' => ['nome' => 'Gestor Patrimonial', 'cor' => 'bg-blue-500', 'icone' => 'briefcase'],
        'tecnico_manutencao' => ['nome' => 'Técnico de Manutenção', 'cor' => 'bg-yellow-400', 'icone' => 'tool'],
        'tecnico_contabilidade' => ['nome' => 'Técnico de Contabilidade', 'cor' => 'bg-green-500', 'icone' => 'refresh-cw'],
        'tecnico_cadastro' => ['nome' => 'Técnico de Cadastro', 'cor' => 'bg-indigo-500', 'icone' => 'database'],
        'padrao' => ['nome' => 'Usuário Padrão', 'cor' => 'bg-gray-400', 'icone' => 'user'],
    ];

    $perfil = auth()->user()->perfil;
    $perfilInfo = $perfisTraduzidos[$perfil] ?? ['nome' => ucfirst($perfil), 'cor' => 'bg-gray-400', 'icone' => 'user'];

    // Resumo de permissões baseado no perfil
    $permissoesResumo = [
        'administrador' => 'Acesso completo a todos os módulos: Ativos, Manutenções, Reavaliações e Configurações.',
        'gestor' => 'Gerencie ativos, dados mestres, manutenções e reavaliações.',
        'tecnico_manutencao' => 'Consulta ativos. Registra e acompanha manutenções nos ativos.',
        'tecnico_contabilidade' => 'Acompanhe e registre reavaliações de ativos.',
        'tecnico_cadastro' => 'Gerencie e cadastre ativos.',
        'padrao' => 'Consulte ativos disponíveis com acesso limitado.',
    ];
    $resumo = $permissoesResumo[$perfil] ?? 'Acesso básico ao sistema.';
@endphp

<div class="max-w-4xl mx-auto mt-6">
    {{-- Card de Boas-Vindas --}}
    <div class="relative p-6 rounded-xl shadow-lg transform transition duration-500 hover:scale-105 hover:shadow-2xl bg-white flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">

        {{-- Avatar / Foto --}}
        <div class="flex-shrink-0">
            @if(auth()->user()->fotografia)
                <img src="{{ asset('storage/' . auth()->user()->fotografia) }}" alt="Foto de {{ auth()->user()->name }}" class="w-24 h-24 rounded-full object-cover">
            @else
                <div class="w-24 h-24 rounded-full flex items-center justify-center text-white text-4xl font-bold {{ $perfilInfo['cor'] }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            @endif
        </div>

        {{-- Conteúdo --}}
        <div class="flex-1 space-y-2">
            <h2 class="text-2xl font-bold text-gray-800">Olá, {{ auth()->user()->name }}!</h2>
            <p class="text-gray-600 font-semibold inline-flex items-center space-x-2">
                <i data-feather="{{ $perfilInfo['icone'] }}" class="w-5 h-5"></i>
                <span>{{ $perfilInfo['nome'] }}</span>
            </p>
            <p class="text-gray-500 mt-2">{{ $resumo }}</p>

            {{-- Ações rápidas --}}
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('ativos.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition">Ver Ativos</a>
                @if(in_array($perfil, ['administrador','gestor','tecnico_manutencao']))
                    <a href="{{ route('manutencoes.index') }}" class="bg-yellow-400 text-white px-4 py-2 rounded-lg shadow hover:bg-yellow-500 transition">Manutenções</a>
                @endif
                @if(in_array($perfil, ['administrador','gestor','tecnico_contabilidade']))
                    <a href="{{ route('reavaliacoes.index') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition">Reavaliações</a>
                @endif
                @if($perfil === 'administrador')
                    <a href="{{ route('config.index') }}" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">Configurações</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Botões Alterar Senha e Alterar Fotografia --}}
    <div class="mt-6 flex justify-center gap-4">
        <a href="{{ route('senha.alterar') }}" class="bg-gray-800 text-white px-6 py-3 rounded-lg shadow hover:bg-gray-900 transition">
            Alterar Senha
        </a>

        <a href="{{ route('users.fotografia.alterar') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow hover:bg-indigo-700 transition">
            Alterar Fotografia
        </a>
    </div>
</div>

{{-- Feather Icons --}}
<script>
    feather.replace();
</script>
@endsection
