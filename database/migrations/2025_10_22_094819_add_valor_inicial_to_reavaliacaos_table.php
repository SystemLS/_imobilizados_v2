<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reavaliacaos', function (Blueprint $table) {
            $table->decimal('valor_inicial', 15, 2)->nullable()->after('bem_id');
        });
    }

    public function down()
    {
        Schema::table('reavaliacaos', function (Blueprint $table) {
            $table->dropColumn('valor_inicial');
        });
    }
};
