<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinciasSeeder extends Seeder
{
    public function run(): void
    {
        $provincias = [
            'Bengo','Benguela','Bié','Cabinda','Cuando Cubango','Cuanza Norte','Cuanza Sul','Cuílo',
            'Huambo','Huíla','Luanda','Lunda Norte','Lunda Sul','Malanje','Moxico','Namibe','Uíge','Zaire'
        ];

        foreach ($provincias as $nome) {
            DB::table('Provincias')->insert(['Nome' => $nome]);
        }
    }
}
