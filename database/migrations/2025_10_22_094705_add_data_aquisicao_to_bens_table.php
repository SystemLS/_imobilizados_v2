<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('Bens', function (Blueprint $table) {
            $table->date('data_aquisicao')->nullable()->after('ValorInicial');
        });
    }

    public function down()
    {
        Schema::table('Bens', function (Blueprint $table) {
            $table->dropColumn('data_aquisicao');
        });
    }
};
