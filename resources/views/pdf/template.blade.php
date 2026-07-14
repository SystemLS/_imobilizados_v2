<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        @page { margin-top: 40mm; }
        @page :first { margin-top: 30mm; }
body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /** Cabeçalho **/
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 110px;
            border-bottom: 1px solid #000;
            padding: 8px 20px;
            text-align: center;
        }

        header .logo img {
            width: 120px;
            height: auto;
            display: inline-block;
        }

        header .empresa-info {
            text-align: center;
            font-size: 12px;
            line-height: 1.3;
        }
        header .empresa-info strong {
            font-size: 12px;
        }

        /** Rodapé **/
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
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            text-align: center;
            font-size: 14px;
            margin: 20px 0 10px 0;
            color: #000;
        }

        .info {
            margin-bottom: 20px;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 4px;
        }
        .info p { margin: 4px 0; font-size: 12px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 12px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #000;
            color: #fff;
            font-weight: bold;
        }
        tbody tr {
            background-color: #fff;
            color: #000;
        }

        .section-title {
            background-color: #e0e7ff;
            font-weight: bold;
            text-align: left;
            padding: 6px;
            border: 1px solid #000;
            margin-top: 20px;
        }

        .export-info {
            margin-top: 10px;
            font-size: 9px;
            text-align: right;
        }

        .header-gap {
            margin-top: 24px;
        }

        /* Espaço para cabeçalho e rodapé */
        body { margin-top: 0; margin-bottom: 40px; }

    </style>
</head>
<body>

@php
    $logoPath = public_path('imagens/ENDE.png');
    $logoSrc = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

<header>
    <div class="logo">
        @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logotipo ENDE">
        @endif
    </div>
    <div class="empresa-info">
        <strong>Empresa Nacional de Distribuição de Electricidade — EP</strong><br>
        Edifício Sede, Rua Cônego Manuel das Neves, Luanda - Angola
    </div>
</header>

<div class="header-gap"></div>

<footer>
    <div>Empresa Nacional de Distribuição de Electricidade — EP</div>
    <div style="text-align: right;">Tel: (+244) 222 123 456 | Email: contacto@ende.co.ao</div>
</footer>

@yield('conteudo')

<div class="export-info">Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}</div>

</body>
</html>


