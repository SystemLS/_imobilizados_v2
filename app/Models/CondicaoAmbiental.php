<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bem;

class CondicaoAmbiental extends Model
{
    use HasFactory;

    // Nome exato da tabela no SQL Server
    protected $table = 'CondicoesAmbientais';

    // Chave primária
    protected $primaryKey = 'CondicaoAmbientalId';

    // Habilita timestamps (caso existam)
    public $timestamps = true;

    // Campos preenchíveis
    protected $fillable = ['Nome', 'Descricao']; // adicione 'Descricao' se existir no banco

    // Relacionamento com Bens
    public function bens()
    {
        return $this->hasMany(Bem::class, 'CondicaoAmbientalId', 'CondicaoAmbientalId');
    }

    protected $dates = ['created_at', 'updated_at'];

}
