<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProvinciasSeeder::class,
            EdificiosSeeder::class,
            PisosSeeder::class,
            SalasSeeder::class,
            GruposSeeder::class,
            CategoriasSeeder::class,
            SubcategoriasSeeder::class,
            MateriaisSeeder::class,
            EstadoConservacaoSeeder::class,
            CondicoesAmbientaisSeeder::class,
            UsersSeeder::class,
            BensSeeder::class,
            BensMateriaisSeeder::class,
        ]);
    }
}
