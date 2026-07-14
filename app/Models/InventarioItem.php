<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioItem extends Model
{
    protected $fillable = [
        'inventario_id','bem_id','sala_id','conferido_por','status_fisico','observacao','conferido_em'
    ];

    protected $dates = ['conferido_em'];

    public function inventario() { return $this->belongsTo(Inventario::class); }
    public function bem() { return $this->belongsTo(\App\Models\Bem::class); }
    public function sala() { return $this->belongsTo(\App\Models\Sala::class); }
    public function conferente() { return $this->belongsTo(\App\Models\User::class, 'conferido_por'); }
}
