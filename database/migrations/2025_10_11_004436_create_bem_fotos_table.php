<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('BemFotos', function (Blueprint $table) {
            $table->id('FotoId');
            $table->foreignId('BemId')->constrained('Bens', 'BemId');
            $table->string('FilePath', 500);
            $table->integer('Ordem')->nullable();
            $table->timestamp('CapturedAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('BemFotos');
    }
};
