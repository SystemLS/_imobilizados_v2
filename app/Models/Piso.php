<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piso extends Model
{
    use HasFactory;

    protected $primaryKey = 'PisoId';
    protected $fillable = ['Nome', 'EdificioId'];

    public function edificio()
    {
        return $this->belongsTo(Edificio::class, 'EdificioId');
    }

    public function salas()
    {
        return $this->hasMany(Sala::class, 'PisoId');
    }

    public function bens()
    {
        return $this->hasManyThrough(Bem::class, Sala::class, 'PisoId', 'SalaId', 'PisoId', 'SalaId');
    }

}
