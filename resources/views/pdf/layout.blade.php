<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 90px;
            border-bottom: 1px solid #000;
            padding: 8px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #000;
            padding: 5px 20px;
            font-size: 9px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        main {
            margin-top: 95px;
            margin-bottom: 40px;
        }

        .export-info {
            margin-top: 10px;
            font-size: 9px;
            text-align: right;
        }

        {!! $css_global ?? '' !!}
    </style>
</head>
<body>
    <header>
        {!! $header !!}
    </header>

    <footer>
        <span>{{ config('app.name', 'Sistema de Ativos') }}</span>
    </footer>

    <main>
        {!! $descricao_html ?? '' !!}

        @if(isset($resumo))
            <div class="info-box">
                {!! $resumo !!}
            </div>
        @endif

        {!! $conteudo ?? '' !!}

        @if(isset($notas))
            <div class="rodape-info">
                {!! $notas !!}
            </div>
        @endif
    </main>

    <div class="export-info">Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}</div>
</body>
</html>
