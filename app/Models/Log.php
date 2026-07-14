<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'usuario_id',
        'evento',
        'descricao',
    ];

    public $timestamps = true; // usa created_at e updated_at

    // Relacionamento com usuário
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
