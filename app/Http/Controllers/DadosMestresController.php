<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provincia;
use App\Models\Edificio;
use App\Models\Piso;
use App\Models\Sala;
use App\Models\Grupo;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\EstadoConservacao;
use App\Models\CondicaoAmbiental;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class DadosMestresController extends Controller
{
    public function index()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou dados mestres',
                'Usuário ' . $authUser->name . ' acessou a página principal de Dados Mestres.'
            );
        }

        return view('ativos.dados_mestres.index', [
            'provincias' => Provincia::orderBy('Nome')->paginate(5),
            'edificios' => Edificio::with('provincia')->paginate(5),
            'pisos' => Piso::with('edificio')->paginate(5),
            'salas' => Sala::with('piso')->paginate(5),
            'grupos' => Grupo::orderBy('Nome')->paginate(5),
            'categorias' => Categoria::with('grupo')->paginate(5),
            'subcategorias' => Subcategoria::with('categoria')->paginate(5),
            'estadosConservacao' => EstadoConservacao::orderBy('Nome')->paginate(5),
            'condicoesAmbientais' => CondicaoAmbiental::orderBy('Nome')->paginate(5),
            'materiais' => Material::orderBy('Nome')->paginate(5),
        ]);
    }
}
