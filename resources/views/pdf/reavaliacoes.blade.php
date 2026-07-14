<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>

    <style>
        @page { margin-top: 40mm; }
        @page :first { margin-top: 30mm; }
* {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
            margin-top: 0;
            margin-bottom: 40px;
        }

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

        header .logo-header {
            width: 120px;
            height: auto;
            display: inline-block;
        }

        header .header-text {
            text-align: center;
            font-size: 12px;
            color: #000;
            line-height: 1.3;
        }

        header .header-text strong {
            font-size: 12px;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            color: #000;
            border-top: 1px solid #000;
            padding: 5px 20px;
        }

        h1 {
            text-align: center;
            font-size: 14px;
            margin: 20px 0 10px 0;
            color: #000;
        }

        main {
            margin: 0;
        }

        .modulo-descricao {
            background-color: #fff;
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .modulo-descricao p {
            margin: 4px 0;
            color: #000;
            font-size: 12px;
            line-height: 1.5;
        }

        .modulo-descricao strong {
            color: #000;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 12px;
            background-color: #fff;
        }

        thead {
            background-color: #000;
            color: #fff;
        }

        th {
            padding: 6px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 12px;
        }

        td {
            padding: 6px;
            border: 1px solid #000;
            font-size: 12px;
            text-align: center;
        }

        tbody tr {
            background-color: #fff;
            color: #000;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            background-color: #000;
            color: #fff;
        }

        .badge-otimo {
            background-color: #000;
            color: #fff;
        }

        .badge-bom {
            background-color: #000;
            color: #fff;
        }

        .badge-regular {
            background-color: #000;
            color: #fff;
        }

        .badge-ruim {
            background-color: #000;
            color: #fff;
        }

        .rodape-info {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 12px;
            color: #000;
        }

        .export-info {
            margin-top: 10px;
            font-size: 9px;
            text-align: right;
        }

        .header-gap {
            margin-top: 24px;
        }

        .text-muted {
            color: #000;
        }
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
        @if($logoSrc)
            <img src="{{ $logoSrc }}" class="logo-header" alt="Logo ENDE">
        @elseif(!empty($logo))
            <img src="{{ $logo }}" class="logo-header" alt="Logo ENDE">
        @endif
        <div class="header-text">
        <strong>Empresa Nacional de Distribuição de Electricidade — EP</strong><br>
        Edifício Sede, Rua Cônego Manuel das Neves, Luanda - Angola
        </div>
    </header>

    <div class="header-gap"></div>

    <footer>
        <span>{{ config('app.name', 'Sistema de Gestão de Ativos Imobilizado') }} | Gerado por {{ $usuario ?? 'Sistema' }}</span>
    </footer>

    <main>
        <div class="modulo-descricao">
            <p><strong>📈 Relatório de Reavaliações:</strong> {{ $descricao }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Bem</th>
                    <th>Etiqueta</th>
                    <th>Data Reavaliação</th>
                    <th>Valor Anterior</th>
                    <th>Valor Reavaliado</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reavaliacoes as $r)
                    <tr>
                        <td><strong>{{ $r['nome_bem'] ?? '-' }}</strong></td>
                        <td>{{ $r['etiqueta'] ?? '-' }}</td>
                        <td>{{ $r['data'] ?? '-' }}</td>
                        <td class="text-muted">{{ $r['valor_anterior'] ?? '-' }}</td>
                        <td><strong>{{ $r['valor_novo'] ?? '-' }}</strong></td>
                        <td>
                            <span class="badge badge-{{ strtolower($r['estado'] ?? 'regular') }}">
                                {{ $r['estado'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $r['usuario'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8;">
                            Nenhuma reavaliação encontrada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="rodape-info">
            <p><strong>Análise de Reavaliações:</strong></p>
            <p>Este relatório mostra as reavaliações realizadas nos ativos. Incluindo mudanças de valor,
               estado de conservação e ajustes registrados no período.</p>
        </div>
    </main>

    <div class="export-info">Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}</div>
</body>
</html>


