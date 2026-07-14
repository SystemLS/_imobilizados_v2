<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubcategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $subcategorias = [
            ['Nome'=>'Desktop','CategoriaId'=>1],
            ['Nome'=>'Notebook','CategoriaId'=>1],
            ['Nome'=>'Laser','CategoriaId'=>2],
            ['Nome'=>'Jato de Tinta','CategoriaId'=>2],
            ['Nome'=>'Mesa de Escritório','CategoriaId'=>4],
            ['Nome'=>'Cadeira de Escritório','CategoriaId'=>5],
            ['Nome'=>'Pintura','CategoriaId'=>6],
            ['Nome'=>'Escultura','CategoriaId'=>6],
        ];

        DB::table('Subcategorias')->insert($subcategorias);
    }
}
