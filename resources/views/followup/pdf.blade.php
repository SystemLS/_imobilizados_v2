<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório Follow Up #{{ $followUp->id }}</title>
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
            margin: 20px 0 10px 0; /* espaço reduzido após cabeçalho */
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

        .present { background-color: #d1fae5; }  /* opcional: verde claro */
        .absent { background-color: #fee2e2; }   /* opcional: vermelho claro */

        .section-title {
            background-color: #e0e7ff;
            font-weight: bold;
            text-align: left;
            padding: 6px;
            border: 1px solid #000;
            margin-top: 20px;
        }

        .header-gap {
            margin-top: 24px;
        }
        .export-info {
            margin-top: 10px;
            font-size: 9px;
            text-align: right;
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
        Edifício Sede, Rua Cônego Manuel das Neves, Luanda, Angola
    </div>
</header>

<div class="header-gap"></div>

<footer>
    <div>Empresa Nacional de Distribuição de Electricidade — EP</div>
    <div style="text-align: right;">Tel: (+244) 222 123 456 | Email: contacto@ende.co.ao | Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}</div>
</footer>

<h1>Relatório de Follow Up #{{ $followUp->id }}</h1>

<div class="info">
    <p><strong>Responsável:</strong> {{ $followUp->usuario->name ?? 'N/D' }}</p>
    <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($followUp->finalizado_em ?? $followUp->iniciado_em)->format('d/m/Y H:i') }}</p>
    <p><strong>Ativos Encontrados:</strong> {{ $followUp->ativos_encontrados ?? 0 }}</p>
    <p><strong>Ativos Não Encontrados:</strong> {{ $followUp->ativos_nao_encontrados ?? 0 }}</p>
    <p><strong>Localização:</strong>
        Província: {{ optional($followUp->provincia)->Nome ?? '-' }},
        Edifício: {{ optional($followUp->edificio)->Nome ?? '-' }},
        Piso: {{ optional($followUp->piso)->Nome ?? '-' }},
        Sala: {{ optional($followUp->sala)->Nome ?? '-' }}
    </p>
    <p><strong>Observações:</strong> {{ $followUp->observacoes ?? 'Sem Observações' }}</p>
</div>

<p class="section-title">Ativos Conferidos</p>
<table>
    <thead>
        <tr>
            <th>Nº</th>
            <th>Etiqueta</th>
            <th>Nome do Ativo</th>
            <th>Presente</th>
            <th>Estado</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        @foreach($followUp->itens as $i => $item)
            @if($item->presente)
            <tr class="present">
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->etiqueta }}</td>
                <td>{{ $item->nome }}</td>
                <td>Sim</td>
                <td>{{ $item->estado }}</td>
                <td>-</td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>

<p class="section-title">Ativos Ausentes</p>
<table>
    <thead>
        <tr>
            <th>Nº</th>
            <th>Etiqueta</th>
            <th>Nome do Ativo</th>
            <th>Presente</th>
            <th>Estado</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        @php
            $ausentes = $followUp->itens->filter(fn($item) => !$item->presente);
        @endphp

        @if($ausentes->isEmpty())
            <tr>
                <td colspan="6" style="text-align:center; padding:6px;">Todos os ativos foram encontrados</td>
            </tr>
        @else
            @foreach($ausentes as $i => $item)
            <tr class="absent">
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->etiqueta }}</td>
                <td>{{ $item->nome }}</td>
                <td>Não</td>
                <td>{{ $item->estado }}</td>
                <td>Activos não encontrados no local de follow-up</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>

<div class="export-info">Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}</div>

</body>
</html>


