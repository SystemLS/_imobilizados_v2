<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up(): void
{
    Schema::create('follow_up_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('follow_up_id');
        $table->unsignedBigInteger('bem_id'); // referência correta
        $table->string('etiqueta')->nullable();
        $table->string('nome')->nullable();
        $table->boolean('presente')->default(0);
        $table->string('estado')->nullable();
        $table->text('observacao')->nullable();
        $table->timestamps();

        // Foreign keys
        $table->foreign('follow_up_id')
              ->references('id')
              ->on('follow_ups')
              ->onDelete('cascade');

        $table->foreign('bem_id')
              ->references('BemId') // <-- coluna correta da tabela bens
              ->on('bens')
              ->onDelete('cascade');
    });
}


    public function down(): void
    {
        Schema::dropIfExists('follow_up_items');
    }
};
