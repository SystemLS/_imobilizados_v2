<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PowerBIController extends Controller
{
    /**
     * Retorna todos os dados principais para integração Power BI.
     */
    public function dados(Request $request)
    {
        $payload = [
            'usuarios' => DB::table('users')->select('id', 'name', 'email', 'perfil', 'created_at', 'updated_at')->get(),
            'bens' => DB::table('Bens')->get(),
            'manutencoes' => DB::table('manutencaos')->get(),
            'reavaliacoes' => DB::table('reavaliacaos')->get(),
            'salas' => DB::table('Salas')->get(),
            'pisos' => DB::table('Pisos')->get(),
            'edificios' => DB::table('Edificios')->get(),
            'provincias' => DB::table('Provincias')->get(),
            'categorias' => DB::table('Categorias')->get(),
            'subcategorias' => DB::table('Subcategorias')->get(),
            'grupos' => DB::table('grupos')->get(),
            'inventarios' => DB::table('inventarios')->get(),
        ];

        return response()->json($payload);
    }

    /**
     * Retorna uma tabela específica por nome de recurso para Power BI.
     */
    public function resource(string $resource)
    {
        $tables = [
            'usuarios' => 'users',
            'bens' => 'Bens',
            'manutencoes' => 'manutencaos',
            'reavaliacoes' => 'reavaliacaos',
            'salas' => 'Salas',
            'pisos' => 'Pisos',
            'edificios' => 'Edificios',
            'provincias' => 'Provincias',
            'categorias' => 'Categorias',
            'subcategorias' => 'Subcategorias',
            'grupos' => 'grupos',
            'inventarios' => 'inventarios',
        ];

        if (!array_key_exists($resource, $tables)) {
            return response()->json(['message' => 'Recurso não encontrado'], 404);
        }

        return response()->json(DB::table($tables[$resource])->get());
    }
}
