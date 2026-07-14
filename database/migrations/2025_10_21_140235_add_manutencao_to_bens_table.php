<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bens', function (Blueprint $table) {
            $table->text('manutencao')->nullable()->after('Etiqueta');
        });
    }

    public function down(): void
    {
        Schema::table('bens', function (Blueprint $table) {
            $table->dropColumn('manutencao');
        });
    }
};
