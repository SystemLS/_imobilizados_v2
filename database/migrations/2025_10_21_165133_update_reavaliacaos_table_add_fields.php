<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
       Schema::table('reavaliacaos', function (Blueprint $table) {
    if (!Schema::hasColumn('reavaliacaos', 'valor_atualizado')) {
        $table->decimal('valor_atualizado', 15, 2)->nullable()->after('valor_inicial');
    }

    if (!Schema::hasColumn('reavaliacaos', 'taxa_depreciacao')) {
        $table->decimal('taxa_depreciacao', 5, 2)->nullable()->after('valor_atualizado');
    }

    if (!Schema::hasColumn('reavaliacaos', 'vida_util')) {
        $table->integer('vida_util')->nullable()->after('taxa_depreciacao');
    }

    if (!Schema::hasColumn('reavaliacaos', 'metodo')) {
        $table->string('metodo')->default('linear')->after('vida_util');
    }

    if (!Schema::hasColumn('reavaliacaos', 'observacoes')) {
        $table->text('observacoes')->nullable()->after('metodo');
    }

    if (!Schema::hasColumn('reavaliacaos', 'usuario_id')) {
        $table->unsignedBigInteger('usuario_id')->nullable()->after('observacoes');
        $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
    }

    if (!Schema::hasColumn('reavaliacaos', 'data_reavaliacao')) {
        $table->date('data_reavaliacao')->nullable()->after('usuario_id');
    }
});

    }

    public function down()
    {
        Schema::table('reavaliacaos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropColumn([
                'valor_atualizado',
                'taxa_depreciacao',
                'vida_util',
                'metodo',
                'observacoes',
                'usuario_id',
                'data_reavaliacao',
            ]);
        });
    }
};
