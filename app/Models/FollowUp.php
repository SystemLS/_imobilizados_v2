<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;


        protected $casts = [
        'iniciado_em'   => 'datetime',
        'finalizado_em' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    protected $fillable = [
        'sala_id',
        'usuario_id',
        'iniciado_em',
        'finalizado_em',
        'status',
        'observacoes',
        'ativos_encontrados',      // para armazenar total de ativos encontrados
        'ativos_nao_encontrados',  // para armazenar total de ativos não encontrados
        'relatorio_json',           // para armazenar o JSON do relatório
        'follow_up_id',
        'bem_id',
        'piso_id',
        'edificio_id',
        'provincia_id',
    ];

    public function itens()
    {
        return $this->hasMany(FollowUpItem::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

        public function sala()
    {
        return $this->belongsTo(Sala::class, 'sala_id', 'SalaId');
    }

    public function piso()
    {
        return $this->belongsTo(Piso::class, 'piso_id', 'PisoId');
    }

    public function edificio()
    {
        return $this->belongsTo(Edificio::class, 'edificio_id', 'EdificioId');
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id', 'ProvinciaId');
    }


}
