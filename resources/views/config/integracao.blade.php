@extends('layouts.app')

@section('title', 'Configuração - Integração')
@section('page-title', 'Integração Power BI')

@section('content')

<div class="mb-6">
    <nav class="flex flex-wrap gap-2 bg-white border border-gray-200 rounded-full shadow-sm p-1">
        <a href="{{ route('config.index') }}"
           class="px-4 py-2 rounded-full text-sm font-semibold text-gray-600 hover:text-gray-900">
            Usuários
        </a>
        <a href="{{ route('config.integracao') }}"
           class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-600 text-white">
            Integração
        </a>
    </nav>
</div>

<div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
    <div class="flex flex-col lg:flex-row justify-between gap-4">
        <div class="max-w-3xl">
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Power BI</h2>
            <p class="text-gray-600 mb-4">
                Aqui você encontra o caminho para conectar o Power BI aos dados do projeto de Gestão de Ativos.
                Use a integração para criar relatórios e dashboards com todas as informações do sistema.
            </p>
            <div class="space-y-3">
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-sm text-gray-500 mb-1 uppercase tracking-wide">URL de integração</p>
                    <p class="text-sm text-gray-700 break-words">{{ url('/') }}</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-sm text-gray-500 mb-1 uppercase tracking-wide">Como usar</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Abra o Power BI Desktop ou Power BI Service.</li>
                        <li>Use a opção <strong>Obter Dados</strong> e escolha <strong>Web</strong> ou <strong>OData</strong>.</li>
                        <li>Informe o endpoint da API para buscar os dados do projeto.</li>
                        <li>Configure autenticação se necessário usando token Bearer.</li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-sm text-gray-500 mb-1 uppercase tracking-wide">Endpoint JSON</p>
                    <p class="text-sm text-gray-700 break-words">{{ url('/api/powerbi/dados') }}</p>
                    <p class="text-sm text-gray-600 mt-2">Você também pode usar recursos individuais:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li>{{ url('/api/powerbi/usuarios') }}</li>
                        <li>{{ url('/api/powerbi/bens') }}</li>
                        <li>{{ url('/api/powerbi/manutencoes') }}</li>
                        <li>{{ url('/api/powerbi/reavaliacoes') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-blue-600 text-white p-6 flex-1 shadow-lg">
            <h3 class="text-xl font-semibold mb-3">Link para Power BI</h3>
            <p class="text-sm text-blue-100 mb-6">
                Abra a plataforma Power BI para iniciar a criação de relatórios a partir dos dados do sistema.
            </p>
            <a href="https://app.powerbi.com/" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 w-full px-5 py-3 bg-white text-blue-700 font-semibold rounded-full shadow hover:bg-blue-100 transition">
                Abrir Power BI
            </a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 lg:grid-cols-2">
        <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Dados disponíveis</h4>
            <p class="text-sm text-gray-600 mb-3">Use os seguintes endpoints ou crie conexões no Power BI:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li>Usuários</li>
                <li>Bens</li>
                <li>Manutenções</li>
                <li>Reavaliações</li>
                <li>Salas, Edifícios, Pisos e Categorias</li>
            </ul>
        </div>
        <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Importante</h4>
            <p class="text-sm text-gray-600 mb-3">
                Para acessar dados protegidos, use a autenticação via token Bearer. Gere o token no endpoint de login e adicione-o nos cabeçalhos do Power BI.
            </p>
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Cabeçalho de autenticação</p>
                <code class="block p-3 rounded-xl bg-gray-100 text-sm text-gray-700">Authorization: Bearer &lt;seu_token&gt;</code>
            </div>
        </div>
    </div>
</div>

@endsection
