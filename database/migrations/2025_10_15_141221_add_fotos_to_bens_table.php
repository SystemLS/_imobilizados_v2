<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Bens', function (Blueprint $table) {
            $table->string('Foto1')->nullable()->after('Descricao');
            $table->string('Foto2')->nullable()->after('Foto1');
            $table->string('Foto3')->nullable()->after('Foto2');
        });
    }

    public function down(): void
    {
        Schema::table('Bens', function (Blueprint $table) {
            $table->dropColumn(['Foto1', 'Foto2', 'Foto3']);
        });
    }
};
