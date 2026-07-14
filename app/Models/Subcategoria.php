<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    use HasFactory;

    protected $primaryKey = 'SubcategoriaId';
    protected $fillable = ['CategoriaId', 'Nome'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'CategoriaId');
    }

    public function bens()
    {
        return $this->hasMany(Bem::class, 'SubcategoriaId');
    }
}
