<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BemFoto extends Model
{
    use HasFactory;

    protected $table = 'BemFotos';
    protected $primaryKey = 'FotoId';
    public $timestamps = false;

    protected $fillable = ['BemId', 'FilePath', 'Ordem', 'CapturedAt'];

    public function bem()
    {
        return $this->belongsTo(Bem::class, 'BemId');
    }
}
