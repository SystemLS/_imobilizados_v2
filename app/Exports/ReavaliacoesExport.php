<?php

namespace App\Exports;

use App\Models\Bem;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ReavaliacoesExport implements FromGenerator, WithHeadings
{
    /**
     * Retorna os dados para o Excel
     */
    public function generator(): \Generator
    {
        $bens = Bem::with([
            'reavaliacoes.usuario',
            'manutencoes',
            'grupo',
            'categoria',
            'subcategoria',
            'sala.piso.edificio.provincia'
        ])->lazyById(200);

        foreach ($bens as $bem) {
            $reavaliacoes = $bem->reavaliacoes->isNotEmpty() ? $bem->reavaliacoes : [null];
            $manutencoes = $bem->manutencoes->isNotEmpty() ? $bem->manutencoes : [null];

            foreach ($reavaliacoes as $r) {
                foreach ($manutencoes as $m) {
                    yield [
                        $bem->Nome ?? '-',
                        $bem->Etiqueta ?? '-',
                        $bem->sala?->piso?->edificio?->provincia?->Nome ?? '-',
                        $bem->sala?->piso?->edificio?->Nome ?? '-',
                        $bem->sala?->piso?->Nome ?? '-',
                        $bem->sala?->Nome ?? '-',
                        $bem->grupo?->Nome ?? '-',
                        $bem->categoria?->Nome ?? '-',
                        $bem->subcategoria?->Nome ?? '-',
                        $bem->preco_aquisicao ?? 0,
                        $bem->data_aquisicao ? Carbon::parse($bem->data_aquisicao)->format('d/m/Y') : '-',
                        $r?->valor_inicial ?? 0,
                        $r?->valor_atualizado ?? 0,
                        $r?->taxa_depreciacao ?? 0,
                        ($r && $r->data_reavaliacao) ? Carbon::parse($r->data_reavaliacao)->format('d/m/Y') : '-',
                        $r?->observacoes ?? '-',
                        $m?->tipo ?? '-',
                        ($m && $m->data_manutencao) ? Carbon::parse($m->data_manutencao)->format('d/m/Y') : '-',
                        $r?->usuario?->name ?? '-',
                    ];
                }
            }
        }
    }

    /**
     * Cabeçalhos do Excel
     */
    public function headings(): array
    {
        return [
            'Activo', 'Etiqueta', 'Província', 'Edifício', 'Piso', 'Sala',
            'Grupo', 'Categoria', 'Subcategoria',
            'Preço Aquisição', 'Data Aquisição',
            'Valor Inicial Reav.', 'Valor Atualizado', 'Taxa Depreciação (%)', 'Data Reavaliação', 'Observações Reav.',
            'Manutenção Tipo', 'Manutenção Data', 'Responsável'
        ];
    }
}
