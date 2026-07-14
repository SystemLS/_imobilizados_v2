<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManutencaosTable extends Migration
{
    public function up()
    {
        Schema::create('manutencaos', function (Blueprint $table) {
            $table->id(); // id da própria tabela manutencaos
            $table->unsignedBigInteger('bem_id'); // coluna que será FK
            $table->string('tipo'); // Preventiva ou corretiva
            $table->text('descricao')->nullable();
            $table->date('data_manutencao');
            $table->string('responsavel')->nullable();
            $table->timestamps();

            // Foreign key ajustada para BemId
            $table->foreign('bem_id')
                  ->references('BemId') // nome da PK na tabela bens
                  ->on('bens')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('manutencaos');
    }
}
