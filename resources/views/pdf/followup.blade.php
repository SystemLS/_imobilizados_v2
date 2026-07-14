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
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.4;
        }

        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 80px;
            overflow: hidden;
            border-bottom: 3px solid #1e3a8a;
            padding: 0 15px;
            text-align: center;
        }

        .logo-header {
            height: 50px;
            display: inline-block;
        }

        .header-text {
            text-align: center;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.2;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding: 5px 15px;
        }

        main {
            margin-top: 20px;
        }

        .modulo-descricao {
            background-color: #f8fafc;
            border-left: 4px solid #dc2626;
            padding: 10px;
            margin-bottom: 15px;
        }

        .modulo-descricao p {
            margin: 0;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.5;
        }

        .modulo-descricao strong {
            color: #1e3a8a;
        }

        .resumo-box {
            background-color: #eef2ff;
            border-left: 4px solid #0369a1;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }

        .resumo-box p {
            margin: 5px 0;
            font-size: 9px;
        }

        .resumo-box strong {
            color: #1e3a8a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #1e3a8a;
            color: white;
        }

        th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }

        td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }

        tbody tr:nth-child(odd) {
            background-color: #f8fafc;
        }

        tbody tr:nth-child(even) {
            background-color: white;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-concluido {
            background-color: #16a34a;
            color: white;
        }

        .badge-pendente {
            background-color: #dc2626;
            color: white;
        }

        .badge-em_progresso {
            background-color: #ea580c;
            color: white;
        }

        .badge-cancelado {
            background-color: #94a3b8;
            color: white;
        }

        .rodape-info {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
        }

        .export-info {
            margin-top: 10px;
            font-size: 9px;
            text-align: right;
            color: #94a3b8;
        }

        .text-muted {
            color: #94a3b8;
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
            <p><strong>✅ Relatório de Acompanhamento (Follow-Up):</strong> {{ $descricao }}</p>
        </div>

        @if(isset($resumo))
            <div class="resumo-box">
                <p><strong>Resumo do Follow-Up:</strong></p>
                <p>Total de Itens: <strong>{{ $resumo['total'] ?? 0 }}</strong> |
                   Concluídos: <strong style="color: #16a34a;">{{ $resumo['concluidos'] ?? 0 }}</strong> |
                   Pendentes: <strong style="color: #dc2626;">{{ $resumo['pendentes'] ?? 0 }}</strong> |
                   Em Progresso: <strong style="color: #ea580c;">{{ $resumo['em_progresso'] ?? 0 }}</strong></p>
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Atividade</th>
                    <th>Bem Associado</th>
                    <th>Data Prevista</th>
                    <th>Status</th>
                    <th>Responsável</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itens as $item)
                    <tr>
                        <td><strong>{{ $item['atividade'] ?? '-' }}</strong></td>
                        <td>{{ $item['bem'] ?? '-' }}</td>
                        <td>{{ $item['data'] ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($item['status'] ?? 'pendente') }}">
                                {{ $item['status'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $item['responsavel'] ?? '-' }}</td>
                        <td class="text-muted">{{ $item['observacoes'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8;">
                            Nenhum item de acompanhamento encontrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="rodape-info">
            <p><strong>Informações sobre o Follow-Up:</strong></p>
            <p>Este relatório contém o acompanhamento de atividades e tarefas relacionadas aos ativos.
               É utilizado para garantir que ações de manutenção, correção e acompanhamento sejam executadas
               conforme planejado.</p>
        </div>
    </main>

    <div class="export-info">Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}</div>
</body>
</html>


        .header-gap {
            margin-top: 24px;
        }
