<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BensMaterial extends Model
{
    use HasFactory;

    protected $table = 'BensMateriais';
    protected $primaryKey = 'BemMaterialId';
    public $timestamps = true;

    protected $fillable = [
        'BemId',
        'MaterialId',
        'Quantidade',
        'Unidade',
        'Observacao',
    ];

    // Relação com Bem
    public function bem()
    {
        return $this->belongsTo(Bem::class, 'BemId');
    }

    // Relação com Material
    public function material()
    {
        return $this->belongsTo(Material::class, 'MaterialId');
    }
}
