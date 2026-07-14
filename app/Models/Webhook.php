<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'evento',
        'ativo',
        'tentativas_falhas',
        'ultima_tentativa',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'tentativas_falhas' => 'integer',
        'ultima_tentativa' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
