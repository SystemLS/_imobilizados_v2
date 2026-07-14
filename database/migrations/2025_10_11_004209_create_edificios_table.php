<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Edificios', function (Blueprint $table) {
            $table->id('EdificioId');
            $table->string('Nome', 250);
            $table->foreignId('ProvinciaId')->nullable()->constrained('Provincias', 'ProvinciaId');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Edificios');
    }
};
