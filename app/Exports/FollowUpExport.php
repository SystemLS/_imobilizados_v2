<?php

namespace App\Exports;

use App\Models\FollowUp;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FollowUpExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnFormatting
{
    protected $followUp;

    public function __construct(FollowUp $followUp)
    {
        $this->followUp = $followUp;
    }

    /**
     * Monta os dados do Excel
     */
    public function array(): array
    {
        $data = [];

        // Resumo do FollowUp
        $data[] = ['Relatório FollowUp #' . $this->followUp->id];
        $data[] = ['Sala', $this->followUp->sala->Nome ?? 'N/D'];
        $data[] = ['Responsável', $this->followUp->usuario->name ?? 'N/D'];
        $data[] = ['Data', \Carbon\Carbon::parse($this->followUp->finalizado_em ?? $this->followUp->iniciado_em)->format('d/m/Y H:i')];
        $data[] = ['Ativos Encontrados', $this->followUp->ativos_encontrados ?? 0];
        $data[] = ['Ativos Não Encontrados', $this->followUp->ativos_nao_encontrados ?? 0];
        $data[] = ['Observações', $this->followUp->observacoes ?? 'Sem Observações'];
        $data[] = []; // linha em branco

        // Cabeçalho da tabela de ativos
        $data[] = ['Nº','Etiqueta','Nome do Ativo','Presente','Estado','Observação'];

        // Dados dos itens
        foreach($this->followUp->itens as $i => $item){
            $data[] = [
                $i + 1,
                $item->etiqueta,
                $item->nome,
                $item->presente ? 'Sim' : 'Não',
                $item->estado,
                $item->observacao ?? 'Sem Observações'
            ];
        }

        return $data;
    }

    /**
     * Cabeçalhos (não é obrigatório neste caso, array já inclui)
     */
    public function headings(): array
    {
        return [];
    }

    /**
     * Estilos das células
     */
    public function styles(Worksheet $sheet)
    {
        // Fonte em negrito para resumo
        $sheet->getStyle('A1:B7')->getFont()->setBold(true);

        // Cabeçalho da tabela
        $startHeaderRow = 9; // considerando as linhas do resumo + linha em branco
        $sheet->getStyle("A{$startHeaderRow}:F{$startHeaderRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$startHeaderRow}:F{$startHeaderRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD1D5DB'); // cinza claro

        // Largura automática das colunas
        foreach(range('A','F') as $col){
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Destacar ativos ausentes (linha vermelha clara)
        $dataStartRow = $startHeaderRow + 1;
        foreach($this->followUp->itens as $i => $item){
            $row = $dataStartRow + $i;
            if(!$item->presente){
                $sheet->getStyle("A{$row}:F{$row}")
                      ->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFFEE2E2'); // vermelho claro
            } else {
                $sheet->getStyle("A{$row}:F{$row}")
                      ->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFD1FAE5'); // verde claro
            }
        }
    }

    /**
     * Título da aba
     */
    public function title(): string
    {
        return 'FollowUp #' . $this->followUp->id;
    }

    /**
     * Formatação de colunas (opcional)
     */
    public function columnFormats(): array
    {
        return [
            'D' => '@', // Presente como texto
        ];
    }
}
