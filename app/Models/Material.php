<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    // Nome real da tabela no SQL Server
    protected $table = 'materiais';
    protected $primaryKey = 'MaterialId';
    protected $fillable = ['Nome'];

    public function bens()
{
    return $this->belongsToMany(
        Bem::class,
        'BensMateriais',
        'MaterialId',
        'BemId'
    )->withPivot(['Quantidade', 'Unidade', 'Observacao'])
     ->withTimestamps();
}

}
