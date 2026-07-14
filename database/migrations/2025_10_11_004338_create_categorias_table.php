<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Categorias', function (Blueprint $table) {
            $table->id('CategoriaId');
            $table->foreignId('GrupoId')->constrained('Grupos', 'GrupoId');
            $table->string('Nome', 150);
            $table->timestamps(); // cria created_at e updated_at

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Categorias');
    }
};
