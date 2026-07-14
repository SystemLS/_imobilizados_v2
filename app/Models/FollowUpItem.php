<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUpItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'follow_up_id',
        'bem_id',
        'etiqueta',
        'nome',
        'presente',
        'estado',
        'observacao',
        'sala_nome',      // nome da sala no momento do follow up
        'piso_nome',      // nome do piso
        'edificio_nome',  // nome do edifício
        'provincia_nome'  // nome da província
    ];

    public function followUp()
    {
        return $this->belongsTo(FollowUp::class);
    }

    public function bem()
    {
        return $this->belongsTo(Bem::class);
    }

    protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];

}
