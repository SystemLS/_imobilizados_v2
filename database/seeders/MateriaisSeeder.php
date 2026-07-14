<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaisSeeder extends Seeder
{
    public function run(): void
    {
        $materiais = [
            'Metal','Madeira','Plástico','Vidro','Cerâmica','Couro'
        ];

        foreach ($materiais as $nome) {
            DB::table('Materiais')->insert(['Nome'=>$nome]);
        }
    }
}
