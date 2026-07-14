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

        h2 {
            font-size: 14px;
            margin: 15px 0 10px 0;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
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
            font-size: 11px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #000;
            color: #fff;
            font-weight: bold;
            text-align: center;
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

<h1>{{ $titulo }}</h1>

<div class="info">
    <p><strong>Descrição:</strong> {{ $descricao }}</p>
</div>

@if(isset($resumo))
    <div class="section-title">Resumo</div>
    <div class="info">
        <p><strong>Total de Ativos:</strong> {{ $resumo['total'] ?? 0 }}</p>
        <p><strong>Grupos:</strong> {{ $resumo['grupos'] ?? 0 }}</p>
        <p><strong>Categorias:</strong> {{ $resumo['categorias'] ?? 0 }}</p>
        <p><strong>Localizações:</strong> {{ $resumo['localizacoes'] ?? 0 }}</p>
    </div>
@endif

<table>
    <thead>
        <tr>
            <th>Etiqueta</th>
            <th>Nome</th>
            <th>Grupo</th>
            <th>Categoria</th>
            <th>Localização</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bens as $bem)
            <tr>
                <td><strong>{{ $bem['etiqueta'] }}</strong></td>
                <td>{{ $bem['nome'] }}</td>
                <td>{{ $bem['grupo'] }}</td>
                <td>{{ $bem['categoria'] }}</td>
                <td>{{ $bem['localizacao'] }}</td>
                <td>{{ $bem['estado'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align: center; padding:10px;">Nenhum ativo encontrado</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px; font-size: 10px;">
    <p><strong>Informações Adicionais:</strong></p>
    <p>Este relatório foi gerado automaticamente pelo Sistema de Gestão de Ativos. Os dados apresentados refletem o estado do sistema no momento da geração.</p>
</div>

<div class="export-info">Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}</div>

</body>
</html>


