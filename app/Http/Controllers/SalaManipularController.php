<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sala;
use App\Models\Piso;
use App\Models\Edificio;
use App\Models\Provincia;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class SalaManipularController extends Controller
{
    public function index(Request $request)
    {
        $query = Sala::with('piso.edificio.provincia');

        if ($request->filled('provincia')) {
            $query->whereHas('piso.edificio.provincia', fn($q) => $q->where('ProvinciaId', $request->provincia));
        }

        if ($request->filled('edificio')) {
            $query->whereHas('piso.edificio', fn($q) => $q->where('EdificioId', $request->edificio));
        }

        if ($request->filled('piso')) {
            $query->where('PisoId', $request->piso);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sub) =>
                $sub->where('Nome', 'like', "%{$q}%")
                    ->orWhereHas('piso', fn($x) => $x->where('Nome', 'like', "%{$q}%"))
                    ->orWhereHas('piso.edificio', fn($x) => $x->where('Nome', 'like', "%{$q}%"))
                    ->orWhereHas('piso.edificio.provincia', fn($x) => $x->where('Nome', 'like', "%{$q}%"))
            );
        }

        $salas = $query->paginate(10);
        $provincias = Provincia::orderBy('Nome')->get();
        $edificios = Edificio::orderBy('Nome')->get();
        $pisos = Piso::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou lista de salas', "Usuário {$authUser->name} acessou a lista de salas.");
        }

        return view('ativos.dados_mestres.salas.index', compact('salas', 'provincias', 'edificios', 'pisos'));
    }

    public function create()
    {
        $provincias = Provincia::orderBy('Nome')->get();
        $edificios = Edificio::orderBy('Nome')->get();
        $pisos = Piso::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou criação de sala', "Usuário {$authUser->name} abriu o formulário para criar sala.");
        }

        return view('ativos.dados_mestres.salas.create', compact('provincias', 'edificios', 'pisos'))->with('success', 'Sala criada com sucesso!');;
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nome' => 'required|string|max:255',
            'PisoId' => 'required|integer|exists:Pisos,PisoId',
        ]);

        $duplicada = Sala::where('Nome', $request->Nome)
            ->where('PisoId', $request->PisoId)
            ->first();

        if ($duplicada) {
            return redirect()->back()->withInput()->withErrors(['Nome' => 'Já existe uma sala com este nome neste piso.']);
        }

        $sala = Sala::create([
            'Nome' => $request->Nome,
            'PisoId' => $request->PisoId,
        ]);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Criou sala', "Usuário {$authUser->name} criou a sala {$sala->Nome}.");
        }

        return redirect()->route('dados_mestres.salas.index')
            ->with('success', '✅ Sala criada com sucesso!');
    }

    public function edit($id)
    {
        $sala = Sala::with('piso.edificio.provincia')->findOrFail($id);
        $provincias = Provincia::orderBy('Nome')->get();
        $edificios = Edificio::orderBy('Nome')->get();
        $pisos = Piso::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou edição de sala', "Usuário {$authUser->name} abriu a sala {$sala->Nome} para edição.");
        }

        return view('ativos.dados_mestres.salas.edit', compact('sala', 'provincias', 'edificios', 'pisos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nome' => 'required|string|max:255',
            'PisoId' => 'required|integer|exists:Pisos,PisoId',
        ]);

        $sala = Sala::findOrFail($id);

        $duplicada = Sala::where('Nome', $request->Nome)
            ->where('PisoId', $request->PisoId)
            ->where('SalaId', '<>', $sala->SalaId)
            ->first();

        if ($duplicada) {
            return redirect()->back()->withInput()->withErrors(['Nome' => 'Já existe uma sala com este nome neste piso.']);
        }

        $sala->update([
            'Nome' => $request->Nome,
            'PisoId' => $request->PisoId,
        ]);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Atualizou sala', "Usuário {$authUser->name} atualizou a sala {$sala->Nome}.");
        }

        return redirect()->route('dados_mestres.salas.index')
            ->with('success', '✅ Sala atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $sala = Sala::findOrFail($id);

        try {
            $sala->delete();

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Excluiu sala', "Usuário {$authUser->name} removeu a sala {$sala->Nome}.");
            }

            return redirect()->route('dados_mestres.salas.index')
                ->with('success', '🗑️ Sala eliminada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('dados_mestres.salas.index')
                ->with('error', 'Não foi possível remover a sala. Ela pode estar em uso.');
        }
    }

    public function verificarDuplicada(Request $request)
    {
        $nome = $request->get('Nome');
        $pisoId = $request->get('PisoId');

        $existe = Sala::where('Nome', $nome)
            ->where('PisoId', $pisoId)
            ->exists();

        return response()->json(['existe' => $existe]);
    }
}
