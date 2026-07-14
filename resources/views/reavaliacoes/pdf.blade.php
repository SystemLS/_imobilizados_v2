<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório Completo de Activos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1, h2, h3 { margin: 10px 0; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; page-break-inside: auto; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; font-size: 11px; }
        th { background-color: #f2f2f2; }
        .valor { font-weight: bold; color: green; }
        .section-title { background-color: #e0e0e0; font-weight: bold; text-align: left; padding: 6px; }
        .subsection { margin-top: 15px; page-break-inside: avoid; }
        hr { border: 1px dashed #ccc; margin: 20px 0; }
    </style>
</head>
<body>
<h1>Relatório Completo de Activos</h1>

@foreach($bens as $bem)
    <div class="subsection">
        <h2>Activo: {{ $bem->Nome ?? '-' }} (Etiqueta: {{ $bem->Etiqueta ?? '-' }})</h2>

        <table>
            <tbody>
                <tr>
                    <td><strong>Nome</strong></td>
                    <td>{{ $bem->Nome ?? '-' }}</td>
                    <td><strong>Etiqueta</strong></td>
                    <td>{{ $bem->Etiqueta ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Província</strong></td>
                    <td>{{ optional($bem->sala?->piso?->edificio?->provincia)->Nome ?? '-' }}</td>
                    <td><strong>Edifício</strong></td>
                    <td>{{ optional($bem->sala?->piso?->edificio)->Nome ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Piso</strong></td>
                    <td>{{ optional($bem->sala?->piso)->Nome ?? '-' }}</td>
                    <td><strong>Sala</strong></td>
                    <td>{{ optional($bem->sala)->Nome ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Preço de Aquisição</strong></td>
                    <td>{{ number_format($bem->preco_aquisicao ?? 0, 2, ',', '.') }} Kz</td>
                    <td><strong>Data de Aquisição</strong></td>
                    <td>{{ $bem->data_aquisicao ? \Carbon\Carbon::parse($bem->data_aquisicao)->format('Y-m-d') : '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Valor Reavaliado</strong></td>
                    <td>{{ number_format($bem->valor_reavaliado ?? 0, 2, ',', '.') }} Kz</td>
                    <td><strong>Grupo</strong></td>
                    <td>{{ optional($bem->grupo)->Nome ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Categoria</strong></td>
                    <td>{{ optional($bem->categoria)->Nome ?? '-' }}</td>
                    <td><strong>Subcategoria</strong></td>
                    <td>{{ optional($bem->subcategoria)->Nome ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Reavaliações -->
        @if($bem->reavaliacoes->count() > 0)
            <h3 class="section-title">Reavaliações</h3>
            <table>
                <thead>
                    <tr>
                        <th>Valor Inicial</th>
                        <th>Taxa Depreciação (%)</th>
                        <th>Anos Uso</th>
                        <th>Valor Líquido Contabilístico</th>
                        <th>Valor Justo</th>
                        <th>Valor Residual</th>
                        <th>Vida Útil Restante</th>
                        <th>Valor Atualizado</th>
                        <th>Nova Depreciação</th>
                        <th>Data Reavaliação</th>
                        <th>Observações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bem->reavaliacoes as $r)
                        <tr>
                            <td>{{ number_format($r->valor_inicial ?? 0, 2, ',', '.') }} Kz</td>
                            <td>{{ number_format($r->taxa_depreciacao ?? 0, 2, ',', '.') }}%</td>
                            <td>{{ number_format($r->anos_uso ?? 0, 2, ',', '.') }}</td>
                            <td>{{ number_format($r->valor_liquido_contabilistico ?? 0, 2, ',', '.') }} Kz</td>
                            <td>{{ number_format($r->valor_justo ?? 0, 2, ',', '.') }} Kz</td>
                            <td>{{ number_format($r->valor_residual ?? 0, 2, ',', '.') }} Kz</td>
                            <td>{{ number_format($r->vida_util_restante ?? 0, 2, ',', '.') }}</td>
                            <td>{{ number_format($r->valor_atualizado ?? 0, 2, ',', '.') }} Kz</td>
                            <td>{{ number_format($r->nova_depreciacao ?? 0, 2, ',', '.') }} Kz/ano</td>
                            <td>{{ $r->data_reavaliacao ? \Carbon\Carbon::parse($r->data_reavaliacao)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $r->observacoes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nenhuma reavaliação registada para este activo.</p>
        @endif

        <!-- Manutenções -->
        @if($bem->manutencoes->count() > 0)
            <h3 class="section-title">Manutenções</h3>
            <table>
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Data Manutenção</th>
                        <th>Custo (Kz)</th>
                        <th>Observações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bem->manutencoes as $m)
                        <tr>
                            <td>{{ $m->descricao ?? '-' }}</td>
                            <td>{{ $m->data_manutencao ? \Carbon\Carbon::parse($m->data_manutencao)->format('Y-m-d') : '-' }}</td>
                            <td>{{ number_format($m->custo ?? 0, 2, ',', '.') }}</td>
                            <td>{{ $m->observacao ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nenhuma manutenção registada para este activo.</p>
        @endif

        <hr>
    </div>
@endforeach
</body>
</html>
