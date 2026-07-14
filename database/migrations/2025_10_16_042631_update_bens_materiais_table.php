<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('BensMateriais', function (Blueprint $table) {

            // Adicionar novas colunas
            if (!Schema::hasColumn('BensMateriais', 'Quantidade')) {
                $table->decimal('Quantidade', 10, 2)->default(1);
            }
            if (!Schema::hasColumn('BensMateriais', 'Unidade')) {
                $table->string('Unidade', 50)->nullable();
            }
            if (!Schema::hasColumn('BensMateriais', 'Observacao')) {
                $table->string('Observacao', 255)->nullable();
            }

            // Adicionar foreign keys (se não existirem)
            $table->foreign('BemId', 'FK_BensMateriais_Bens')
                  ->references('BemId')->on('Bens')
                  ->onDelete('cascade');

            $table->foreign('MaterialId', 'FK_BensMateriais_Materiais')
                  ->references('MaterialId')->on('Materiais')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('BensMateriais', function (Blueprint $table) {
            $table->dropForeign('FK_BensMateriais_Bens');
            $table->dropForeign('FK_BensMateriais_Materiais');
            $table->dropColumn(['Quantidade','Unidade','Observacao']);
        });
    }
};
