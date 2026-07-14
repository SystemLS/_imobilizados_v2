<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sala;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class SalaController extends Controller
{
    // Retorna todas as salas
    public function index()
    {
        $salas = Sala::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou lista de salas', "Usuário {$authUser->name} acessou a lista de salas.");
        }

        return response()->json($salas);
    }

    // Retorna salas de acordo com o piso
    public function getSalasPorPiso($pisoId)
    {
        $salas = Sala::where('PisoId', $pisoId)->orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou salas por piso', "Usuário {$authUser->name} acessou as salas do PisoId {$pisoId}.");
        }

        return response()->json($salas);
    }

    // Cria uma nova sala (via QR Code)
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        // Verifica se a sala já existe
        $existing = Sala::where('Nome', $request->nome)->first();
        if ($existing) {
            if ($authUser = Auth::user()) {
                LogHelper::registrar('Tentativa de criar sala duplicada', "Usuário {$authUser->name} tentou criar a sala {$request->nome}, mas ela já existia.");
            }
            return response()->json($existing, 200); // Retorna sala existente
        }

        // Cria nova sala
        $sala = Sala::create([
            'Nome' => $request->nome,
            'PisoId' => $request->piso_id ?? null, // Opcional
        ]);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Criou sala', "Usuário {$authUser->name} criou a sala {$sala->Nome}.");
        }

        return response()->json($sala, 201)->with('success', 'Sala criada com sucesso!');
    }
}
