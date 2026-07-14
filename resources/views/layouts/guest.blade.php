<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ===== BASE ===== */
        body {
            background-color: #111827;
            color: #f3f4f6;
            font-family: 'Figtree', sans-serif;
            margin: 0;
            padding: 0;
        }

        .bg-cover-center {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .bg-image-filter::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
        }

        /* ===== LOGIN BOX ===== */
        .login-box {
            background-color: #1f2937;
            border-radius: 20px;
            padding: 60px 50px 40px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.35);
            position: relative;
            max-width: 400px;
            width: 100%;
        }

        .login-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        /* ===== LOGO ===== */
        .login-logo {
            width: 85px;
            height: 85px;
            position: absolute;
            top: -42px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 12px;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            object-fit: contain;
        }

        /* ===== INPUTS E BOTÕES ===== */
        input, select {
            border-radius: 10px !important;
            border: 1px solid #374151;
            background-color: #111827;
            color: #f3f4f6;
            transition: all 0.3s ease;
            width: 100%;
            padding: 10px;
        }

        input:focus, select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        label, a {
            color: #f3f4f6 !important;
        }

        a:hover {
            color: #93c5fd !important;
        }

        /* ===== BOTÃO ENTRAR ===== */
        button {
            background-color: #3b82f6;
            border-radius: 10px;
            padding: 10px 24px;
            transition: background 0.3s;
            font-weight: 600;
        }

        button:hover {
            background-color: #2563eb;
        }

        /* ===== FOOTER ===== */
        footer {
            margin-top: 25px;
            text-align: center;
            font-size: 0.85rem;
            color: #d1d5db;
        }

        /* ===== ANIMAÇÃO ===== */
        .fade-in {
            opacity: 0;
            transform: translateY(25px);
            transition: all 0.6s ease;
        }

        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== MOBILE ===== */
        @media (max-width: 640px) {
            .login-box {
                padding: 40px 25px 30px;
                margin-top: 50px;
                max-width: 90%;
                border-radius: 15px;
            }



            @media (max-width: 640px) {
    /* Outros estilos mobile... */

    /* Botão curto para redefinir senha */
    .btn-short {
        width: auto;           /* Ajusta a largura ao conteúdo */
        padding-left: 16px;    /* Ajusta o espaçamento interno horizontal */
        padding-right: 16px;
        display: inline-block; /* Garante que respeite o tamanho do texto */
        text-align: center;
    }
}



            .login-logo {
                width: 70px;
                height: 70px;
                top: -35px;
            }

            input, select {
                font-size: 0.9rem;
                padding: 8px;
            }

            button {
                width: 100%;
                margin-top: 10px;
                font-size: 0.95rem;
            }

            footer {
                position: relative;
                margin-top: 30px;
                font-size: 0.8rem;
                color: #9ca3af;
            }
        }
    </style>
</head>

<body class="antialiased">

    <div class="min-h-screen w-full relative bg-gray-900 overflow-hidden">
        {{-- Fundo --}}
        <div class="absolute inset-0 bg-cover-center bg-image-filter"
             style="background-image: url('{{ asset('imagens/Capa_login.jpg') }}');"></div>

        {{-- Conteúdo central --}}
        <div class="relative z-20 min-h-screen flex flex-col justify-center items-center py-10 px-4">

            {{-- Box de login --}}
            <div class="fade-in delay-300">
                <div class="login-box">

                    {{-- Logo --}}
                    <img src="{{ asset('imagens/ENDE.png') }}" alt="Logo ENDE" class="login-logo">

                    {{-- Formulário (conteúdo Blade dinâmico) --}}
                    {{ $slot }}

                    {{-- Rodapé dentro do container --}}
                    <footer>
                        © {{ date('Y') }} {{ config('app.name', 'Gestão de Ativos') }} — Todos os direitos reservados
                    </footer>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para animação --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".fade-in").forEach((el, i) => {
                setTimeout(() => el.classList.add("show"), i * 200);
            });
        });
    </script>
</body>
</html>
