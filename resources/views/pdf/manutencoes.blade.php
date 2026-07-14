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

        .resumo-box {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .resumo-item {
            background-color: #fff;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 4px;
        }

        .resumo-item .label {
            font-size: 11px;
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
        }

        .resumo-item .valor {
            font-size: 14px;
            color: #000;
            font-weight: bold;
            margin-top: 5px;
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

        .badge-concluida {
            background-color: #000;
            color: #fff;
        }

        .badge-pendente {
            background-color: #000;
            color: #fff;
        }

        .badge-em_progresso {
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
            <p><strong>🔧 Relatório de Manutenções:</strong> {{ $descricao }}</p>
        </div>

        @if(isset($resumo))
            <div class="resumo-box">
                <div class="resumo-item">
                    <div class="label">Total de Registros</div>
                    <div class="valor">{{ $resumo['total'] }}</div>
                </div>
                <div class="resumo-item">
                    <div class="label">Concluídas</div>
                    <div class="valor" style="color: #16a34a;">{{ $resumo['concluidas'] }}</div>
                </div>
                <div class="resumo-item">
                    <div class="label">Pendentes</div>
                    <div class="valor" style="color: #dc2626;">{{ $resumo['pendentes'] }}</div>
                </div>
                <div class="resumo-item">
                    <div class="label">Em Progresso</div>
                    <div class="valor" style="color: #ea580c;">{{ $resumo['em_progresso'] }}</div>
                </div>
            </div>
        @endif

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
                @forelse($manutencoes as $m)
                    <tr>
                        <td><strong>{{ $m['Nome'] ?? '-' }}</strong></td>
                        <td>{{ $m['Etiqueta'] ?? '-' }}</td>
                        <td>{{ $m['TipoManutencao'] ?? '-' }}</td>
                        <td>{{ $m['DataInicio'] ?? '-' }}</td>
                        <td>{{ $m['DataConclusao'] ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($m['Status'] ?? 'pendente') }}">
                                {{ $m['Status'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $m['Responsavel'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8;">
                            Nenhuma manutenção encontrada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="rodape-info">
            <p><strong>Notas:</strong></p>
            <p>Este relatório apresenta o histórico de manutenções registradas no sistema.
               Os dados incluem manutenções preventivas, corretivas e preditivas.</p>
        </div>
    </main>

    <div class="export-info">Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}</div>
</body>
</html>


