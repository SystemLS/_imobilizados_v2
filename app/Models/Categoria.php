<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $primaryKey = 'CategoriaId';
    protected $fillable = ['GrupoId', 'Nome'];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'GrupoId');
    }

    public function subcategorias()
    {
        return $this->hasMany(Subcategoria::class, 'CategoriaId');
    }

    public function bens()
    {
        return $this->hasMany(Bem::class, 'CategoriaId', 'CategoriaId');
    }



}
