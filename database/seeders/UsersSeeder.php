<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name'=>'Administrador',
            'email'=>'admin@sistema.com',
            'password'=>Hash::make('admin123'),
            'perfil'=>'administrador',
            'created_at'=>now(),
            'updated_at'=>now()
        ]);
    }
}
