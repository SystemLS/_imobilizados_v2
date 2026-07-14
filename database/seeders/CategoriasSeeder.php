<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        // Exemplo: atribuindo categorias a cada grupo (GrupoId começa em 1)
        $categorias = [
            ['Nome'=>'Computadores','GrupoId'=>1],
            ['Nome'=>'Impressoras','GrupoId'=>1],
            ['Nome'=>'Móveis de Escritório','GrupoId'=>3],
            ['Nome'=>'Mesas','GrupoId'=>3],
            ['Nome'=>'Cadeiras','GrupoId'=>3],
            ['Nome'=>'Quadros','GrupoId'=>4],
        ];

        DB::table('Categorias')->insert($categorias);
    }
}
