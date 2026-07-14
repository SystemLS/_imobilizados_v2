<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReavaliacaosTable extends Migration
{
    public function up()
    {
        Schema::create('reavaliacaos', function (Blueprint $table) {
            $table->id(); // id da própria tabela reavaliacao
            $table->unsignedBigInteger('bem_id'); // FK para bens
            $table->decimal('valor_atualizado', 15, 2); // novo valor do bem
            $table->date('data_reavaliacao');
            $table->text('observacoes')->nullable();
            $table->string('responsavel')->nullable();
            $table->timestamps();

            // Foreign key ajustada para BemId
            $table->foreign('bem_id')
                  ->references('BemId') // coluna PK da tabela bens
                  ->on('bens')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reavaliacaos');
    }
}
