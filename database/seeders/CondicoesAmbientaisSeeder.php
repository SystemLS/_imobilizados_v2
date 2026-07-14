<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CondicoesAmbientaisSeeder extends Seeder
{
    public function run(): void
    {
        $condicoes = ['Normal','Humidade','Calor','Frio','Poluído'];
        foreach ($condicoes as $nome) {
            DB::table('CondicoesAmbientais')->insert(['Nome'=>$nome]);
        }
    }
}
