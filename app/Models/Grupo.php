<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';
    protected $primaryKey = 'GrupoId';

    // Laravel vai popular automaticamente created_at e updated_at
    public $timestamps = true;

    protected $fillable = [
        'Nome'
    ];

    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'GrupoId');
    }
}
