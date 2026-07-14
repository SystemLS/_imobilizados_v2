<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manutencao extends Model
{
    use HasFactory;

    protected $table = 'manutencaos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int'; // bigint é compatível com int no Eloquent

    protected $fillable = [
        'bem_id',
        'tipo',
        'descricao',
        'data_manutencao',
        'responsavel',
        'status',
        'DataConclusao',
    ];

    // Indica que esses campos devem ser tratados como objetos Carbon
    protected $casts = [
        'data_manutencao' => 'datetime',
        'data_conclusao'  => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];


    public function bem()
    {
        return $this->belongsTo(Bem::class, 'bem_id', 'BemId');
    }
}
