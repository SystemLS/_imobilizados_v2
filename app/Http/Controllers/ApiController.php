<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function getEdificios($provinciaId)
    {
        $edificios = DB::table('Edificios')
            ->where('ProvinciaId', $provinciaId)
            ->select('EdificioId', 'Nome')
            ->orderBy('Nome')
            ->get();

        return response()->json($edificios);
    }

    public function getPisos($edificioId)
    {
        $pisos = DB::table('Pisos')
            ->where('EdificioId', $edificioId)
            ->select('PisoId', 'Nome')
            ->orderBy('Nome')
            ->get();

        return response()->json($pisos);
    }

    public function getSalas($pisoId)
    {
        $salas = DB::table('Salas')
            ->where('PisoId', $pisoId)
            ->select('SalaId', 'Nome')
            ->orderBy('Nome')
            ->get();

        return response()->json($salas);
    }

    public function getCategorias($grupoId)
    {
        $categorias = DB::table('Categorias')
            ->where('GrupoId', $grupoId)
            ->select('CategoriaId', 'Nome')
            ->orderBy('Nome')
            ->get();

        return response()->json($categorias);
    }

    public function getSubcategorias($categoriaId)
    {
        $subcategorias = DB::table('Subcategorias')
            ->where('CategoriaId', $categoriaId)
            ->select('SubcategoriaId', 'Nome')
            ->orderBy('Nome')
            ->get();

        return response()->json($subcategorias);
    }
}
