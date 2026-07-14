<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestão Imobilizado')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('/public/favicon.ico') }}">

    <style>
        .active-nav-item { background-color: rgba(59,130,246,0.1); border-left: 3px solid #3b82f6; }
        .content-area { height: calc(100vh - 65px - 50px); }
        [x-cloak] { display: none !important; }
        .tracejado { border-top: 1px dashed #cbd5e1; margin: 0.5rem 0; }
    </style>

    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
</head>

<body class="bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300" x-data="{ sidebarOpen: false }">
<div class="flex h-screen">

    @php $perfil = auth()->user()->perfil; @endphp

    {{-- Sidebar responsiva --}}
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
         class="sidebar -translate-x-full bg-white/90 backdrop-blur-lg w-64 shadow-2xl fixed h-full transform transition-transform duration-300 ease-in-out z-50 md:translate-x-0 flex flex-col justify-between">

        {{-- Overlay mobile --}}
        <div @click="sidebarOpen = false"
             x-cloak
             x-show="sidebarOpen"
             class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 md:hidden"></div>

        <div class="relative z-50 flex flex-col justify-between h-full">

            {{-- Topo com logo CENTRALIZADO --}}
            <div>
                <div class="p-4 border-b flex justify-center relative">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('imagens/ENDE.png') }}" alt="Logo"
                             class="w-20 h-20 object-contain rounded-full mx-auto">
                    </a>

                    <button @click="sidebarOpen = false"
                            class="md:hidden absolute right-4 top-6 text-gray-600 hover:text-red-500">
                        <i data-feather="x"></i>
                    </button>
                </div>

                @auth
                    @php
                        $user = auth()->user();
                        $nomeUsuario = $user->name;
                        $iniciais = collect(explode(' ', $nomeUsuario))->map(fn($n) => substr($n,0,1))->join('');
                        $perfisTraduzidos = [
                            'administrador' => 'Administrador',
                            'gestor' => 'Gestor Patrimonial',
                            'tecnico_manutencao' => 'Técnico de Manutenção',
                            'tecnico_contabilidade' => 'Técnico de Contabilidade',
                            'tecnico_cadastro' => 'Técnico de Cadastro',
                            'padrao' => 'Usuário Padrão',
                        ];
                        $perfilFormatado = $perfisTraduzidos[$user->perfil] ?? ucfirst($user->perfil);
                    @endphp

                    {{-- Perfil Desktop --}}
                    <div class="p-4 hidden md:flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center overflow-hidden border-2 border-gray-300">
                            @if($user->fotografia)
                                <img src="{{ asset('storage/' . $user->fotografia) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold">
                                    {{ strtoupper($iniciais) }}
                                </div>
                            @endif
                        </div>
                        <p class="mt-2 text-gray-700 font-medium text-center">{{ $nomeUsuario }}</p>
                        <p class="text-sm text-gray-500">{{ $perfilFormatado }}</p>
                        <a href="{{ route('painel.controle') }}" class="mt-2 w-full text-center bg-gray-100 px-3 py-2 rounded-lg hover:bg-gray-200">
                            Meu Perfil
                        </a>
                        <div class="tracejado"></div>
                    </div>

                    {{-- Perfil Mobile --}}
                    <div class="p-4 md:hidden">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden border-2 border-gray-300">
                                @if($user->fotografia)
                                    <img src="{{ asset('storage/' . $user->fotografia) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper($iniciais) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-gray-700 font-medium">{{ $nomeUsuario }}</p>
                                <p class="text-xs text-gray-500">{{ $perfilFormatado }}</p>
                            </div>
                        </div>

                        <div class="mt-2">
                            <a href="{{ route('painel.controle') }}"
                               class="w-full block bg-gray-100 px-3 py-2 rounded-lg hover:bg-gray-200 text-sm text-center">
                                Perfil
                            </a>
                        </div>
                    </div>
                @endauth

                {{-- Menus COMPLETOS --}}
                <div class="p-4 flex-1">
                    <nav class="space-y-1">

                        <a href="{{ route('dashboard') }}"
                           class="flex items-center space-x-3 p-2 rounded-lg {{ request()->is('dashboard') ? 'active-nav-item text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i data-feather="grid"></i><span>Dashboard</span>
                        </a>

                        {{-- Gestão de Activos --}}
                        <div x-data="{ openAtivos: {{ request()->routeIs('ativos.*') || request()->routeIs('dados_mestres.*') || request()->routeIs('inventario.*') ? 'true' : 'false' }} }">
                            <button @click="openAtivos = !openAtivos"
                                    class="w-full flex items-center justify-between p-2 rounded-lg text-gray-600 hover:bg-gray-100 {{ request()->routeIs('ativos.*') || request()->routeIs('dados_mestres.*') || request()->routeIs('inventario.*') ? 'active-nav-item text-blue-600' : '' }}">
                                <span class="flex items-center space-x-3">
                                    <i data-feather="box"></i><span>Gestão de Activos</span>
                                </span>
                                <svg class="w-4 h-4 transform transition-transform duration-200"
                                     :class="{'rotate-90': openAtivos}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <div x-cloak x-show="openAtivos" class="ml-8 space-y-1" x-transition>
                                <a href="{{ route('ativos.index') }}"
                                   class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 {{ request()->routeIs('ativos.*') ? 'text-blue-600 font-semibold bg-gray-100' : '' }}">
                                    <i data-feather="list"></i><span>Listar Activos</span>
                                </a>
                                <a href="{{ route('dados_mestres.index') }}"
                                   class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 {{ request()->routeIs('dados_mestres.*') ? 'text-blue-600 font-semibold bg-gray-100' : '' }}">
                                    <i data-feather="database"></i><span>Dados Mestres</span>
                                </a>

                                @if(in_array(Auth::user()->perfil, ['administrador', 'gestor', 'tecnico_cadastro']))
                                    <a href="{{ route('inventario.index') }}"
                                       class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 {{ request()->routeIs('inventario.*') ? 'text-blue-600 font-semibold bg-gray-100' : '' }}">
                                        <i data-feather="clipboard"></i><span>Inventário</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Manutenções --}}
                        @if(in_array($perfil, ['administrador','gestor','tecnico_manutencao']))
                            <a href="{{ route('manutencoes.index') }}"
                               class="flex items-center space-x-3 p-2 rounded-lg {{ request()->routeIs('manutencoes.*') ? 'active-nav-item text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                <i data-feather="tool"></i><span>Manutenções</span>
                            </a>
                        @endif

                        {{-- Reavaliações --}}
                        @if(in_array($perfil, ['administrador','gestor','tecnico_contabilidade']))
                            <a href="{{ route('reavaliacoes.index') }}"
                               class="flex items-center space-x-3 p-2 rounded-lg {{ request()->is('reavaliacoes*') ? 'active-nav-item text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                                <i data-feather="refresh-cw"></i><span>Reavaliações</span>
                            </a>
                        @endif

                        {{-- Configurações --}}
                        @if($perfil === 'administrador')
                            <div x-data="{ openConfig: {{ request()->is('config*') ? 'true' : 'false' }} }">
                                <button @click="openConfig = !openConfig"
                                        class="w-full flex items-center justify-between p-2 rounded-lg text-gray-600 hover:bg-gray-100 {{ request()->is('config*') ? 'active-nav-item text-blue-600' : '' }}">
                                    <span class="flex items-center space-x-3">
                                        <i data-feather="settings"></i><span>Configurações</span>
                                    </span>
                                    <svg class="w-4 h-4 transform transition-transform duration-200"
                                         :class="{'rotate-90': openConfig}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>

                                <div x-cloak x-show="openConfig" x-transition class="ml-8 space-y-1">
                                    <a href="{{ route('config.index') }}"
                                       class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 {{ request()->routeIs('config.index') ? 'text-blue-600 font-semibold bg-gray-100' : '' }}">
                                        <i data-feather="user"></i><span>Gerir Usuários</span>
                                    </a>
                                    <a href="{{ route('config.integracao') }}"
                                       class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 {{ request()->routeIs('config.integracao') ? 'text-blue-600 font-semibold bg-gray-100' : '' }}">
                                        <i data-feather="bar-chart-2"></i><span>Integração</span>
                                    </a>
                                    <a href="{{ route('logs.index') }}"
                                       class="flex items-center space-x-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 {{ request()->routeIs('logs.*') ? 'text-blue-600 font-semibold bg-gray-100' : '' }}">
                                        <i data-feather="file-text"></i><span>Logs do Sistema</span>
                                    </a>
                                </div>
                            </div>
                        @endif

                    </nav>
                </div>
            </div>

            {{-- Botões Sair --}}
            <div class="p-4 border-t hidden md:block">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        <i data-feather="log-out"></i><span>Sair</span>
                    </button>
                </form>
            </div>

            <div class="p-4 border-t md:hidden mt-auto">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        <i data-feather="log-out"></i><span>Sair</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- Conteúdo principal --}}
    <div class="flex-1 md:ml-64 flex flex-col transition-all duration-300">

        {{-- Header SEM RELÓGIO --}}
        <header class="bg-white shadow-sm p-4 flex items-center sticky top-0 z-40">
            <button @click="sidebarOpen = true" class="md:hidden text-gray-600 hover:text-blue-600 mr-4">
                <i data-feather="menu"></i>
            </button>

            <h2 class="text-2xl font-bold text-gray-800">
                Sistema de Gestão de Activos Imobilizados
            </h2>
        </header>

        <main class="content-area overflow-y-auto p-6 flex-1">
            @yield('content')
        </main>

        <footer class="bg-white shadow-inner text-center text-gray-600 py-4 border-t border-gray-200">
            <p class="text-sm">
                &copy; {{ date('Y') }} Sistema de Gestão de Ativos.
                Desenvolvido por <strong>Leonildo Caculo</strong>.
            </p>
        </footer>
    </div>
</div>

<script>
    AOS.init();
    feather.replace();
</script>
<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
