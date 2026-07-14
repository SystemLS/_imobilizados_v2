<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Salas', function (Blueprint $table) {
            $table->id('SalaId');
            $table->string('Nome', 150);
            $table->foreignId('PisoId')->constrained('Pisos', 'PisoId');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Salas');
    }
};
