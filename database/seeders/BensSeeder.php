<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BensSeeder extends Seeder
{
    public function run(): void
    {
        $bens = [
            [
                'Nome' => 'Desktop Dell OptiPlex',
                'SalaId' => 1,
                'SubcategoriaId' => 1, // Desktop
                'Etiqueta' => 'ETQ001',
                'Marca' => 'Dell',
                'Modelo' => 'OptiPlex 3080',
                'TipoNumeroSerie' => 'SN',
                'NumeroSerieManual' => '12345',
                'NumeroScanner' => 'D12345',
                'Capacidade' => '8GB RAM',
                'Potencia' => '250W',
                'Descricao' => 'Desktop para escritório',
                'EstadoConservacaoId' => 1, // Novo
                'CondicaoAmbientalId' => 1, // Normal
                'preco_aquisicao' => 800000,
                'valor_depreciado' => 0,
                'valor_reavaliado' => 800000,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'Nome' => 'Notebook HP ProBook',
                'SalaId' => 2,
                'SubcategoriaId' => 2, // Notebook
                'Etiqueta' => 'ETQ002',
                'Marca' => 'HP',
                'Modelo' => 'ProBook 450',
                'TipoNumeroSerie' => 'SN',
                'NumeroSerieManual' => '67890',
                'NumeroScanner' => 'N67890',
                'Capacidade' => '16GB RAM',
                'Potencia' => '65W',
                'Descricao' => 'Notebook administrativo',
                'EstadoConservacaoId' => 2, // Bom
                'CondicaoAmbientalId' => 1, // Normal
                'preco_aquisicao' => 1200000,
                'valor_depreciado' => 200000,
                'valor_reavaliado' => 1000000,
                'created_at'=>now(),
                'updated_at'=>now()
            ]
        ];

        DB::table('Bens')->insert($bens);
    }
}
