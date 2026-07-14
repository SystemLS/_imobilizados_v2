<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $fillable = ['codigo','descricao','data_inicio','data_fim','responsavel_id','status','metadados'];

    protected $casts = ['metadados' => 'array', 'data_inicio' => 'datetime', 'data_fim' => 'datetime'];

    public function responsavel() { return $this->belongsTo(\App\Models\User::class, 'responsavel_id'); }
    public function itens() { return $this->hasMany(InventarioItem::class); }
}
