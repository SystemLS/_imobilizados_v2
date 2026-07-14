<?php

namespace App\Exports;

use App\Models\Manutencao;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ManutencoesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithChunkReading
{
    protected $filtros;

    public function __construct(array $filtros = [])
    {
        $this->filtros = $filtros;
    }

    /**
     * Buscar dados com filtros
     */
    public function query()
    {
        $query = Manutencao::with('bem');

        if (!empty($this->filtros['bem'])) {
            $query->whereHas('bem', function ($q) {
                $q->where('Nome', 'like', '%' . $this->filtros['bem'] . '%');
            });
        }

        if (!empty($this->filtros['etiqueta'])) {
            $query->whereHas('bem', function ($q) {
                $q->where('Etiqueta', 'like', '%' . $this->filtros['etiqueta'] . '%');
            });
        }

        if (!empty($this->filtros['tipo'])) {
            $query->where('tipo', $this->filtros['tipo']);
        }

        if (!empty($this->filtros['responsavel'])) {
            $query->where('responsavel', 'like', '%' . $this->filtros['responsavel'] . '%');
        }

        if (!empty($this->filtros['status'])) {
            $query->where('status', $this->filtros['status']);
        }

        return $query->orderByDesc('data_manutencao');
    }

    /**
     * Cabeçalhos do Excel
     */
    public function headings(): array
    {
        return [
            'Bem',
            'Etiqueta',
            'Tipo de Manutenção',
            'Data da Manutenção',
            'Data da Conclusão',
            'Status',
            'Responsável',
        ];
    }

    /**
     * Mapear linhas
     */
    public function map($manutencao): array
    {
        return [
            $manutencao->bem->Nome ?? '-',
            $manutencao->bem->Etiqueta ?? '-',
            $manutencao->tipo,
            optional($manutencao->data_manutencao)->format('d/m/Y'),
            $manutencao->DataConclusao
                ? optional($manutencao->DataConclusao)->format('d/m/Y')
                : '-',
            $manutencao->status,
            $manutencao->responsavel,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
