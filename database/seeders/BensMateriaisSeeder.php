<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BensMateriaisSeeder extends Seeder
{
    public function run(): void
    {
        // Associando materiais aos bens
        $bensMateriais = [
            ['BemId'=>1, 'MaterialId'=>1], // Desktop -> Metal
            ['BemId'=>1, 'MaterialId'=>3], // Desktop -> Plástico
            ['BemId'=>2, 'MaterialId'=>1], // Notebook -> Metal
            ['BemId'=>2, 'MaterialId'=>3], // Notebook -> Plástico
        ];

        DB::table('BensMateriais')->insert($bensMateriais);
    }
}
