<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reavaliacao extends Model
{
    use HasFactory;

    // Nome da tabela e chave primária
    protected $table = 'reavaliacaos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'bem_id',
        'usuario_id',
        'valor_inicial',
        'taxa_depreciacao',
        'vida_util',
        'data_aquisicao',
        'vlc',
        'nova_depreciacao_anual',
        'valor_atualizado',
        'data_reavaliacao',
        'observacoes',
        'valor_residual',
        'metodo',
    ];


    // Conversão de tipos automáticos
    protected $casts = [
        'valor_inicial' => 'decimal:2',
        'taxa_depreciacao' => 'decimal:2',
        'vlc' => 'decimal:2',
        'nova_depreciacao_anual' => 'decimal:2',
        'anos_uso' => 'decimal:2',
        'valor_liquido_contabilistico' => 'decimal:2',
        'valor_justo' => 'decimal:2',
        'valor_residual' => 'decimal:2',
        'vida_util_restante' => 'decimal:2',
        'valor_atualizado' => 'decimal:2',
        'nova_depreciacao' => 'decimal:2',
        'data_aquisicao' => 'date',
        'data_reavaliacao' => 'date',
    ];

    // -------------------------
    // RELACIONAMENTOS
    // -------------------------

    /**
     * Reavaliação pertence a um Bem.
     */
    public function bem()
    {
        return $this->belongsTo(Bem::class, 'bem_id', 'BemId');
    }

    /**
     * Reavaliação pertence a um Usuário (quem realizou a reavaliação).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }
}
