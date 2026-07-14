<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::create('follow_ups', function (Blueprint $table) {
        $table->id(); // id do follow_up
        $table->unsignedBigInteger('sala_id');
        $table->unsignedBigInteger('usuario_id');
        $table->timestamp('iniciado_em')->nullable();
        $table->timestamp('finalizado_em')->nullable();
        $table->string('status')->default('pendente');
        $table->integer('ativos_encontrados')->default(0);
        $table->integer('ativos_nao_encontrados')->default(0);
        $table->text('observacoes')->nullable();
        $table->json('relatorio_json')->nullable();
        $table->timestamps();

        // Ajuste aqui: referência à coluna correta da tabela salas
        $table->foreign('sala_id')
              ->references('SalaId') // <-- coluna correta
              ->on('salas')
              ->onDelete('cascade');

        $table->foreign('usuario_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
    });
}


    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
