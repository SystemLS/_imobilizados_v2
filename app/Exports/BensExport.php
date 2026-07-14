<?php

namespace App\Exports;

use App\Models\Bem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BensExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
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
        ];
    }

    public function headings(): array
    {
        return ['Etiqueta', 'Nome', 'Grupo', 'Categoria', 'Localização', 'Estado'];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
