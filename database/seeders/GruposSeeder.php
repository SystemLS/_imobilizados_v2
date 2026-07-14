<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GruposSeeder extends Seeder
{
    public function run(): void
    {
        $grupos = [
            'EQUIPAMENTOS','MATERIAL','MOBILIARIO','OBRAS DE ARTE','TARAS E VASILHAS','SÍMBOLOS PLACAS DE IDENTIFICAÇÃO E SINALIZAÇÃO'
        ];

        foreach ($grupos as $nome) {
            DB::table('Grupos')->insert(['Nome'=>$nome]);
        }
    }
}
