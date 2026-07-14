
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Logs - Relatório</title>
    <style>
        @page { margin-top: 40mm; }
        @page :first { margin-top: 30mm; }
body { font-family: Arial, sans-serif; }
    </style>
</head>
<body>
    <h1>Template PDF de Logs Removido</h1>
    <p>Esta funcionalidade foi removida conforme solicitado.</p>
</body>
</html>
            align-items: center;
            font-size: 9px;
            color: #000;
            border-top: 1px solid #000;
            padding: 5px 20px;
        }

        h1 {
            text-align: center;
            font-size: 22px;
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

        .badge-create {
            background-color: #000;
            color: #fff;
        }

        .badge-update {
            background-color: #000;
            color: #fff;
        }

        .badge-delete {
            background-color: #000;
            color: #fff;
        }

        .badge-view {
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
            <img src="{{ $logoSrc }}" class="logo-header" alt="Logo ENDE" style="display:block;margin:0 auto;width:120px;height:auto;">
        @elseif(!empty($logo))
            <img src="{{ $logo }}" class="logo-header" alt="Logo ENDE" style="display:block;margin:0 auto;width:120px;height:auto;">
        @endif
        <div class="header-text" style="font-size: 12px; line-height: 1.3; text-align: center;">
        <strong>Empresa Nacional de Distribuição de Electricidade — EP</strong><br>
        Edifício Sede, Rua Cônego Manuel das Neves, Luanda - Angola
        </div>
    </header>

    <div style="margin-top: 24px;"></div>

    <footer>
        <span>{{ config('app.name', 'Sistema de Gestão de Ativos Imobilizado') }} | Gerado por {{ $usuario ?? 'Sistema' }}</span>
    </footer>

    <main>
        <div class="modulo-descricao">
            <p><strong>📝 Relatório de Auditoria:</strong> {{ $descricao }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Tipo</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs_formatados as $log)
                    <tr>
                        <td><strong>{{ $log['data_hora'] ?? '-' }}</strong></td>
                        <td>{{ $log['usuario'] ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($log['tipo'] ?? 'view') }}">
                                {{ $log['tipo'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td>{{ $log['acao'] ?? '-' }}</td>
                        <td class="text-muted">{{ $log['detalhes'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8;">
                            Nenhum log encontrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="rodape-info">
            <p><strong>Relatório de Auditoria do Sistema:</strong></p>
            <p>Este relatório documenta todas as operações realizadas no sistema, servindo para rastreabilidade
               e conformidade. Inclui operações de criação, leitura, atualização e exclusão de dados.</p>
        </div>
    </main>

    <div style="margin-top: 10px; font-size: 9px; text-align: right;">
        Exportado em {{ $data_geracao ?? now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>


