<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('BensMateriais', function (Blueprint $table) {
            $table->foreignId('BemId')->constrained('Bens', 'BemId');
            $table->foreignId('MaterialId')->constrained('Materiais', 'MaterialId');
            $table->primary(['BemId','MaterialId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('BensMateriais');
    }
};
