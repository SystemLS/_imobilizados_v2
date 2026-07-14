<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PisosSeeder extends Seeder
{
    public function run(): void
    {
        // Edificio 1
        $pisos = ['Cave','R/C','1º Andar','2º Andar','3º Andar','4º Andar','9º Andar','10º Andar','11º Andar','12º Andar','13º Andar','14º Andar'];
        foreach ($pisos as $nome) {
            DB::table('Pisos')->insert(['Nome'=>$nome,'EdificioId'=>1]);
        }

        // Edificio 2
        DB::table('Pisos')->insert([['Nome'=>'R/C','EdificioId'=>2],['Nome'=>'1º Andar','EdificioId'=>2]]);
        // Edificio 3
        DB::table('Pisos')->insert([['Nome'=>'R/C','EdificioId'=>3],['Nome'=>'1º Andar','EdificioId'=>3]]);
        // Edificio 4
        DB::table('Pisos')->insert([['Nome'=>'R/C','EdificioId'=>4],['Nome'=>'1º Andar','EdificioId'=>4]]);
        // Edificio 5
        DB::table('Pisos')->insert([['Nome'=>'R/C','EdificioId'=>5],['Nome'=>'1º Andar','EdificioId'=>5]]);
    }
}
