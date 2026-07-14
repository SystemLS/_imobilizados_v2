<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Manutenções</title>

    <style>
        @page { margin-top: 40mm; }
        @page :first { margin-top: 30mm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
            padding: 0 15px;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 10px;
            color: #555;
        }

        .logo {
            width: 120px;
            display: inline-block;
        }

        .titulo {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }

        .empresa-info {
            font-size: 12px;
            text-align: center;
            line-height: 1.3;
        }

        .header-gap {
            margin-top: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #444;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .page-break {
            page-break-after: always;
        }

        .export-info {
            margin-top: 10px;
            font-size: 9px;
            text-align: right;
        }
    </style>
</head>
<body>

{{-- Cabeçalho --}}
@php
    $logoPath = public_path('imagens/ENDE.png');
    $logoSrc = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

<header>
    @if($logoSrc)
        <img src="{{ $logoSrc }}" class="logo" alt="Logotipo ENDE">
    @endif
    <div class="titulo">Relatório de Manutenções</div>
    <div class="empresa-info">
        <strong>Empresa Nacional de Distribuição de Electricidade — EP</strong><br>
        Edifício Sede, Rua Cônego Manuel das Neves, Luanda - Angola
    </div>
</header>

<div class="header-gap"></div>

{{-- Rodapé --}}
<footer>
    Relatório de Manutenções |
    Página <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_text(520, 820, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 8, array(0,0,0));
        }
    </script>
</footer>

<main>
    <table>
        <thead>
            <tr>
                <th>Bem</th>
                <th>Etiqueta</th>
                <th>Tipo</th>
                <th>Início</th>
                <th>Conclusão</th>
                <th>Status</th>
                <th>Responsável</th>
            </tr>
        </thead>
        <tbody>
            @foreach($manutencoes as $m)
                <tr>
                    <td>{{ $m->bem->Nome ?? '-' }}</td>
                    <td>{{ $m->bem->Etiqueta ?? '-' }}</td>
                    <td>{{ $m->tipo }}</td>
                    <td>{{ \Carbon\Carbon::parse($m->data_manutencao)->format('d/m/Y') }}</td>
                    <td>
                        {{ $m->DataConclusao ? \Carbon\Carbon::parse($m->DataConclusao)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $m->status }}</td>
                    <td>{{ $m->responsavel }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</main>

<div class="export-info">Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}</div>

</body>
</html>


