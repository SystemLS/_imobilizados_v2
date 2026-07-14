<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventario_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')
                  ->constrained('inventarios')
                  ->cascadeOnDelete();

            // Ajuste: bem_id referencia BemId da tabela bens
            $table->unsignedBigInteger('bem_id')->nullable();
            $table->foreign('bem_id')
                  ->references('BemId')
                  ->on('bens')
                  ->nullOnDelete();

            $table->string('etiqueta')->nullable();
            $table->string('nome')->nullable();
            $table->boolean('presente')->default(false);
            $table->string('estado')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventario_itens');
    }
};
