<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataConclusaoToManutencaosTable extends Migration
{
    public function up(): void
    {
        Schema::table('manutencaos', function (Blueprint $table) {
            $table->dateTime('DataConclusao')->nullable()->after('Data');
            // 'Data' é a coluna de início da manutenção, ajusta se tiver outro nome
        });
    }

    public function down(): void
    {
        Schema::table('manutencaos', function (Blueprint $table) {
            $table->dropColumn('DataConclusao');
        });
    }
}
