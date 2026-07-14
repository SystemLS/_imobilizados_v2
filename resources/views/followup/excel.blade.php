<table style="width:100%; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; font-size: 12px;">
    <thead>
        <!-- Título do relatório -->
        <tr>
            <th colspan="6" style="text-align:center; font-weight:bold; font-size:16px; padding:10px; background-color:#f0f0f0; border:1px solid #ccc;">
                Relatório de Follow Up #{{ $followUp->id }}
            </th>
        </tr>
        <!-- Resumo do FollowUp -->
        <tr style="background-color:#e0e7ff;">
            <th style="padding:5px; border:1px solid #ccc;">Sala</th>
            <th style="padding:5px; border:1px solid #ccc;">Responsável</th>
            <th style="padding:5px; border:1px solid #ccc;">Data</th>
            <th style="padding:5px; border:1px solid #ccc;">Ativos Encontrados</th>
            <th style="padding:5px; border:1px solid #ccc;">Ativos Não Encontrados</th>
            <th style="padding:5px; border:1px solid #ccc;">Observações</th>
        </tr>
        <tr>
            <td style="padding:5px; border:1px solid #ccc;">{{ $followUp->sala->Nome ?? 'N/D' }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ $followUp->usuario->name ?? 'N/D' }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ \Carbon\Carbon::parse($followUp->finalizado_em ?? $followUp->iniciado_em)->format('d/m/Y H:i') }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ $followUp->ativos_encontrados }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ $followUp->ativos_nao_encontrados }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ $followUp->observacoes ?? 'Sem Observações' }}</td>
        </tr>
        <tr><td colspan="6" style="border:none;"></td></tr>
        <!-- Cabeçalho da tabela de ativos -->
        <tr style="background-color:#c7d2fe; font-weight:bold; text-align:center;">
            <th style="padding:5px; border:1px solid #ccc;">#</th>
            <th style="padding:5px; border:1px solid #ccc;">Etiqueta</th>
            <th style="padding:5px; border:1px solid #ccc;">Nome</th>
            <th style="padding:5px; border:1px solid #ccc;">Presente</th>
            <th style="padding:5px; border:1px solid #ccc;">Estado</th>
            <th style="padding:5px; border:1px solid #ccc;">Observação</th>
        </tr>
    </thead>
    <tbody>
        @foreach($followUp->itens as $i => $item)
        <tr style="background-color: {{ $item->presente ? '#d1fae5' : '#fee2e2' }}; text-align:center;">
            <td style="padding:5px; border:1px solid #ccc;">{{ $i + 1 }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ $item->etiqueta }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ $item->nome }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ $item->presente ? 'Sim' : 'Não' }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ $item->estado }}</td>
            <td style="padding:5px; border:1px solid #ccc;">{{ $item->observacao ?? 'Sem Observações' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
