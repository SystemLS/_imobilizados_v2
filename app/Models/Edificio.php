<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edificio extends Model
{
    use HasFactory;

    protected $primaryKey = 'EdificioId';

    // Desativa timestamps (created_at, updated_at)
    public $timestamps = false;

    // Campos preenchíveis
    protected $fillable = [
        'Nome',
        'ProvinciaId',
    ];

    // Relação com Província
    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'ProvinciaId');
    }

    // Relação com Pisos
    public function pisos()
    {
        return $this->hasMany(Piso::class, 'EdificioId');
    }

    // Relação com Bens (através dos Pisos)
    public function bens()
    {
        return $this->hasManyThrough(
            Bem::class,   // Modelo final
            Piso::class,  // Modelo intermediário
            'EdificioId', // FK no modelo intermediário (Piso)
            'PisoId',     // FK no modelo final (Bem)
            'EdificioId', // PK local no Edificio
            'PisoId'      // PK local no Piso
        );
    }
}
