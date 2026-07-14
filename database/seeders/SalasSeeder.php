<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalasSeeder extends Seeder
{
    public function run(): void
    {
        // Exemplo de salas para o Edificio 1, Piso 1
        $salas = [
            ['Nome'=>'Sala 101','PisoId'=>1],
            ['Nome'=>'Sala 102','PisoId'=>1],
            ['Nome'=>'Sala 103','PisoId'=>1],
        ];

        DB::table('Salas')->insert($salas);

        // Adicione mais salas conforme necessário para outros pisos/edificios
    }
}
