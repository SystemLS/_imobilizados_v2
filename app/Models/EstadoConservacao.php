<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bem;

class EstadoConservacao extends Model
{
    use HasFactory;

    // Nome exato da tabela no SQL Server
    protected $table = 'EstadoConservacao';

    // Chave primária
    protected $primaryKey = 'EstadoConservacaoId';

    // Habilita timestamps (created_at e updated_at)
    public $timestamps = true;

    // Campos preenchíveis
    protected $fillable = ['Nome', 'Descricao'];

    // Relacionamento com Bens
    public function bens()
    {
        return $this->hasMany(Bem::class, 'EstadoConservacaoId', 'EstadoConservacaoId');
    }

    // Adicionar esta linha para garantir que Laravel converte corretamente para datetime
    protected $dates = ['created_at', 'updated_at'];
}
