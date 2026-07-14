<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $perfis = ['administrador', 'gestor', 'tecnico_cadastro', 'tecnico_contabilidade', 'tecnico_manutencao', 'padrao'];
        
        foreach ($perfis as $perfil) {
            $email = $perfil . '@ativos.local';
            $exists = DB::table('users')->where('email', $email)->exists();
            
            if (!$exists) {
                DB::table('users')->insert([
                    'name' => ucwords(str_replace('_', ' ', $perfil)),
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'perfil' => $perfil,
                    'force_password_change' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $perfis = ['administrador', 'gestor', 'tecnico_cadastro', 'tecnico_contabilidade', 'tecnico_manutencao', 'padrao'];
        foreach ($perfis as $perfil) {
            DB::table('users')->where('email', $perfil . '@ativos.local')->delete();
        }
    }
};
