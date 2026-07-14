<?php

namespace App\Exports;

use App\Models\Log;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LogsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected array $filtros;

    // Recebe filtros para montar a consulta
    public function __construct(array $filtros = [])
    {
        $this->filtros = $filtros;
    }

    public function query()
    {
        $query = Log::with('usuario')->orderByDesc('created_at');

        $dataInicio = $this->filtros['data_inicio'] ?? null;
        $dataFim = $this->filtros['data_fim'] ?? null;
        $usuarioId = $this->filtros['usuario_id'] ?? null;

        if (!empty($dataInicio) && !empty($dataFim)) {
            $query->whereBetween('created_at', [
                $dataInicio . ' 00:00:00',
                $dataFim . ' 23:59:59'
            ]);
        } elseif (!empty($dataInicio)) {
            $query->whereDate('created_at', '>=', $dataInicio);
        } elseif (!empty($dataFim)) {
            $query->whereDate('created_at', '<=', $dataFim);
        }

        if (!empty($usuarioId)) {
            $query->where('usuario_id', $usuarioId);
        }

        return $query;
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->usuario?->name ?? 'Sistema',
            $log->evento,
            $log->descricao,
            $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-',
        ];
    }

    /**
     * Cabeçalhos da tabela Excel
     */
    public function headings(): array
    {
        return [
            '#',
            'Usuário',
            'Evento',
            'Descrição',
            'Horário',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
