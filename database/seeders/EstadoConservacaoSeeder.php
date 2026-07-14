<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoConservacaoSeeder extends Seeder
{
    public function run(): void
    {
        $estados = ['Novo','Bom','Regular','Mau'];
        foreach ($estados as $nome) {
            DB::table('EstadoConservacao')->insert(['Nome'=>$nome]);
        }
    }
}
