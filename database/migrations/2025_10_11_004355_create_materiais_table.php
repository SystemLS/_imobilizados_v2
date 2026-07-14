<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Materiais', function (Blueprint $table) {
            $table->id('MaterialId');
            $table->string('Nome', 150);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Materiais');
    }
};
