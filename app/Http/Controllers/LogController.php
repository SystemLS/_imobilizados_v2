<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LogsExport;

class LogController extends Controller
{
    /**
     * Exibe logs do sistema com filtros e usuários
     */
public function index(Request $request)
{
    $query = Log::with('usuario')->latest('created_at');

    // Filtro por intervalo de datas
    if ($request->filled('data_inicio') && $request->filled('data_fim')) {
        $query->whereBetween('created_at', [
            $request->data_inicio . ' 00:00:00',
            $request->data_fim . ' 23:59:59'
        ]);
    }

    // Filtro por usuário
    if ($request->filled('usuario_id')) {
        $query->where('usuario_id', $request->usuario_id);
    }

    // Paginação mantendo filtros
    $logs = $query->paginate(20)->withQueryString();

    $usuarios = User::orderBy('name')->get();

    return view('logs.index', compact('logs', 'usuarios'));
}



    /**
     * Exporta logs filtrados para Excel
     */
    public function export(Request $request)
    {
        $filtros = $request->only(['data_inicio', 'data_fim', 'usuario_id']);

        return Excel::download(new LogsExport($filtros), 'logs.xlsx');
    }
}
