<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Remover a constraint antiga (se existir)
            DB::statement("
                ALTER TABLE users
                DROP CONSTRAINT IF EXISTS CK_users_perfil;
            ");

            // Alterar a coluna perfil para NVARCHAR(50)
            DB::statement("
                ALTER TABLE users
                ALTER COLUMN perfil NVARCHAR(50) NOT NULL;
            ");

            // Adicionar novamente a constraint CHECK
            DB::statement("
                ALTER TABLE users
                ADD CONSTRAINT CK_users_perfil CHECK (
                    perfil IN (
                        'administrador',
                        'gestor',
                        'tecnico_cadastro',
                        'tecnico_contabilidade',
                        'tecnico_manutencao',
                        'padrao'
                    )
                );
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS CK_users_perfil;");
            DB::statement("ALTER TABLE users ALTER COLUMN perfil NVARCHAR(50) NULL;");
        }
    }
};
