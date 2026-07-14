<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Bens', function (Blueprint $table) {
            // Adiciona a coluna PisoId (nullable, para não quebrar dados existentes)
            $table->unsignedBigInteger('PisoId')->nullable()->after('SalaId');

            // Adiciona a foreign key relacionando com Pisos
            $table->foreign('PisoId')->references('PisoId')->on('Pisos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('Bens', function (Blueprint $table) {
            $table->dropForeign(['PisoId']);
            $table->dropColumn('PisoId');
        });
    }
};
