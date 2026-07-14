<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BensTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // IDs de referência existentes
        $salas = [1, 2, 3];
        $grupos = [1, 2];
        $categorias = [1, 2];
        $subcategorias = [1, 2];
        $estados = [1, 2];
        $condicoes = 1;
        $imagens = [
            'imagens/AA000981_1_309559700.jpg'

        ];

        for ($i = 1; $i <= 50; $i++) {
            $tipoSerie = $faker->randomElement(['NumeroSerieManual', 'NumeroScanner']);
            DB::table('Bens')->insert([
                'SalaId' => $faker->randomElement($salas),
                'GrupoId' => $faker->randomElement($grupos),
                'CategoriaId' => $faker->randomElement($categorias),
                'SubcategoriaId' => $faker->randomElement($subcategorias),
                'Nome' => $faker->words(3, true),
                'Etiqueta' => 'AA' . $faker->unique()->numberBetween(100000, 999999),
                'Marca' => $faker->company,
                'Modelo' => $faker->bothify('Model-###'),
                'TipoNumeroSerie' => $tipoSerie,
                'NumeroSerieManual' => $tipoSerie === 'NumeroSerieManual' ? $faker->bothify('AA######') : null,
                'NumeroScanner' => $tipoSerie === 'NumeroScanner' ? $faker->bothify('SCN######') : null,
                'Capacidade' => $faker->randomElement(['4 lugares', '500GB', '1TB', '2TB', '16GB RAM']),
                'Descricao' => $faker->sentence(10),
                'EstadoConservacaoId' => $faker->randomElement($estados),
                'CondicaoAmbientalId' => $faker->randomElement($condicoes),
                'preco_aquisicao' => $faker->numberBetween(50000, 500000),
                'Foto1' => $faker->randomElement($imagens),
                'Foto2' => $faker->randomElement($imagens),
                'Foto3' => $faker->randomElement($imagens),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
