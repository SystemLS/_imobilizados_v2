<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Bens', function (Blueprint $table) {
            $table->id('BemId');
            $table->foreignId('SalaId')->nullable()->constrained('Salas', 'SalaId');
            $table->foreignId('SubcategoriaId')->constrained('Subcategorias', 'SubcategoriaId');
            $table->string('Nome', 200);
            $table->string('Etiqueta', 100)->nullable();
            $table->string('Marca', 200)->nullable();
            $table->string('Modelo', 200)->nullable();
            $table->string('TipoNumeroSerie', 100)->nullable();
            $table->string('NumeroSerieManual', 250)->nullable();
            $table->string('NumeroScanner', 250)->nullable();
            $table->string('Capacidade', 100)->nullable();
            $table->string('Potencia', 100)->nullable();
            $table->text('Descricao')->nullable();
            $table->foreignId('EstadoConservacaoId')->nullable()->constrained('EstadoConservacao', 'EstadoConservacaoId');
            $table->foreignId('CondicaoAmbientalId')->nullable()->constrained('CondicoesAmbientais', 'CondicaoAmbientalId');
            $table->decimal('preco_aquisicao', 18, 2)->nullable();
            $table->decimal('valor_depreciado', 18, 2)->nullable();
            $table->decimal('valor_reavaliado', 18, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Bens');
    }
};
