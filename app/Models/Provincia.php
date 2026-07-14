<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;

    // Nome da tabela (opcional se seguir convenção)
    protected $table = 'Provincias';

    // Chave primária
    protected $primaryKey = 'ProvinciaId';

    // Desabilita timestamps (created_at, updated_at)
    public $timestamps = false;

    // Campos que podem ser preenchidos via mass assignment
    protected $fillable = ['Nome'];

    /**
     * Relacionamento com Edificios
     * Uma província pode ter vários edifícios
     */
    public function edificios()
    {
        return $this->hasMany(Edificio::class, 'ProvinciaId');
    }
}
