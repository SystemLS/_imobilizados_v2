<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DadosMestresExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected array $headings;
    protected array $rows;
    protected string $title;

    public function __construct(array $headings, array $rows, string $title = 'Dados Mestres')
    {
        $this->headings = $headings;
        $this->rows = $rows;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:Z' => [
                'alignment' => [
                    'horizontal' => 'left',
                    'vertical' => 'center',
                ],
            ],
        ];
    }

    public function title(): string
    {
        return substr($this->title, 0, 31);
    }
}
