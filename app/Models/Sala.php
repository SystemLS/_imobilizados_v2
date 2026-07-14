<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    use HasFactory;
    
    protected $table = 'Salas';
    protected $primaryKey = 'SalaId';
    protected $fillable = ['Nome', 'PisoId'];
    public $timestamps = false;

    public function piso()
    {
        return $this->belongsTo(Piso::class, 'PisoId');
    }

    public function bens()
    {
        return $this->hasMany(Bem::class, 'SalaId');
    }
}
