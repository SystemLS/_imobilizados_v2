<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bem extends Model
{
    use HasFactory;

    protected $table = 'Bens';
    protected $primaryKey = 'BemId';
    public $timestamps = true;

    protected $fillable = [
        'SalaId',
        'SubcategoriaId',
        'CategoriaId',
        'GrupoId',
        'Nome',
        'Etiqueta',
        'Marca',
        'Modelo',
        'TipoNumeroSerie',
        'NumeroSerieManual',
        'NumeroScanner',
        'Capacidade',
        'Potencia',
        'Descricao',
        'EstadoConservacaoId',
        'CondicaoAmbientalId',
        'preco_aquisicao',
        'valor_depreciado',
        'valor_reavaliado',
        'data_aquisicao', // NOVO CAMPO ADICIONADO
        'Foto1',
        'Foto2',
        'Foto3',
        'manutencao',
    ];

    // -------------------------
    // RELACIONAMENTOS DIRETOS
    // -------------------------
public function sala()
{
    return $this->belongsTo(Sala::class, 'SalaId', 'SalaId');
}

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class, 'SubcategoriaId');
    }

public function categoria()
{
    return $this->belongsTo(Categoria::class, 'CategoriaId', 'CategoriaId');
}

public function grupo()
{
    return $this->belongsTo(Grupo::class, 'GrupoId', 'GrupoId');
}

    public function estadoConservacao()
    {
        return $this->belongsTo(EstadoConservacao::class, 'EstadoConservacaoId', 'EstadoConservacaoId');
    }

    public function condicaoAmbiental()
    {
        return $this->belongsTo(CondicaoAmbiental::class, 'CondicaoAmbientalId');
    }

    public function materiais()
    {
        return $this->belongsToMany(
            Material::class,
            'BensMateriais',
            'BemId',
            'MaterialId'
        );
    }

    public function fotos()
    {
        return $this->hasMany(BemFoto::class, 'BemId');
    }

    public function manutencoes()
    {
        return $this->hasMany(Manutencao::class, 'bem_id', 'BemId');
    }

    public function reavaliacoes()
    {
        return $this->hasMany(Reavaliacao::class, 'bem_id', 'BemId');
    }

    // -------------------------
    // RELACIONAMENTOS CASCATA PARA LOCALIZAÇÃO
    // -------------------------
    public function piso()
    {
        return $this->sala ? $this->sala->piso : null;
    }

    public function edificio()
    {
        return $this->piso() ? $this->piso()->edificio : null;
    }

    public function provincia()
    {
        return $this->edificio() ? $this->edificio()->provincia : null;
    }

    // -------------------------
    // ACESSORES
    // -------------------------
    public function getFotosPrincipaisAttribute()
    {
        return collect([$this->Foto1, $this->Foto2, $this->Foto3])->filter();
    }
}
