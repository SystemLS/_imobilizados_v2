<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('evento'); // bem.criado, bem.atualizado, bem.deletado, manutencao.criada, reavaliacacao.criada
            $table->boolean('ativo')->default(true);
            $table->integer('tentativas_falhas')->default(0);
            $table->timestamp('ultima_tentativa')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'evento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
