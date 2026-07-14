<?php

namespace App\Exports;

use App\Models\Bem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Helpers\CurrencyHelper;

class AtivosExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithChunkReading
{
    public function query()
    {
        return Bem::query()
            ->with(['grupo','categoria','estadoConservacao','sala.piso.edificio.provincia']);
    }

    public function map($bem): array
    {
        $provincia = $bem->sala?->piso?->edificio?->provincia?->Nome;
        $edificio = $bem->sala?->piso?->edificio?->Nome;
        $piso = $bem->sala?->piso?->Nome;
        $sala = $bem->sala?->Nome;
        $localizacao = ($provincia ?? '-') . ' / ' . ($edificio ?? '-') . ' / ' . ($piso ?? '-') . ' / ' . ($sala ?? '-');

        return [
            $bem->Etiqueta ?? '-',
            $bem->Nome,
            $bem->grupo?->Nome ?? '-',
            $bem->categoria?->Nome ?? '-',
            $localizacao,
            $bem->estadoConservacao?->Nome ?? '-',
            $bem->Marca ?? '-',
            $bem->Modelo ?? '-',
            CurrencyHelper::formatKz($bem->preco_aquisicao ?? 0, 2, false),
            $bem->data_aquisicao ? optional($bem->data_aquisicao)->format('d/m/Y') : '-',
        ];
    }

    public function headings(): array
    {
        return ['Etiqueta', 'Nome', 'Grupo', 'Categoria', 'Localização', 'Estado', 'Marca', 'Modelo', 'Preço Aquisição', 'Data Aquisição'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a8a']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
