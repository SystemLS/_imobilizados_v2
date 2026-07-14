<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Pisos', function (Blueprint $table) {
            $table->id('PisoId');
            $table->string('Nome', 100);
            $table->foreignId('EdificioId')->constrained('Edificios', 'EdificioId');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Pisos');
    }
};
