<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdificiosSeeder extends Seeder
{
    public function run(): void
    {
        $edificios = [
            ['Nome' => 'Edificio Sede - AV. CONEGO MANUEL', 'ProvinciaId' => 11],
            ['Nome' => 'SE - Edificio Sede', 'ProvinciaId' => 11],
            ['Nome' => 'Edificio Piquete Sede', 'ProvinciaId' => 11],
            ['Nome' => 'Pavilhão Sede', 'ProvinciaId' => 11],
            ['Nome' => 'Edificio Social Sede', 'ProvinciaId' => 11],
        ];

        DB::table('Edificios')->insert($edificios);
    }
}
