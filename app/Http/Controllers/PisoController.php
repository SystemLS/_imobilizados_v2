<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Piso;
use App\Models\Edificio;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Exception;

class PisoController extends Controller
{
    /**
     * Retorna os pisos de acordo com o edifício
     */
    public function porEdificio($edificioId)
    {
        $pisos = Piso::where('EdificioId', $edificioId)
                     ->orderBy('Nome')
                     ->get(['PisoId', 'Nome']);
        return response()->json($pisos);
    }

    /**
     * Lista de pisos
     */
    public function index()
    {
        $pisos = Piso::with('edificio')->orderBy('EdificioId')->orderBy('Nome')->paginate(10);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou lista de pisos', "Usuário {$authUser->name} visualizou a lista de pisos.");
        }

        return view('ativos.dados_mestres.pisos.index', compact('pisos'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $edificios = Edificio::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou criação de piso', "Usuário {$authUser->name} abriu o formulário para criar piso.");
        }

        return view('ativos.dados_mestres.pisos.create', compact('edificios'));
    }

    /**
     * Armazenar novo piso
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nome' => [
                'required',
                'string',
                'max:100',
                Rule::unique('Pisos')->where(function ($query) use ($request) {
                    return $query->where('EdificioId', $request->EdificioId);
                }),
            ],
            'EdificioId' => 'required|exists:Edificios,EdificioId',
        ], [
            'Nome.unique' => 'Já existe um piso com este nome neste edifício.',
        ]);

        $piso = Piso::create([
            'Nome' => $request->Nome,
            'EdificioId' => $request->EdificioId,
        ]);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Criou piso', "Usuário {$authUser->name} criou o piso '{$piso->Nome}' no edifício ID {$piso->EdificioId}.");
        }

        return redirect()->route('dados_mestres.pisos.index')
                         ->with('success', '✅ Piso criado com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $piso = Piso::findOrFail($id);
        $edificios = Edificio::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou edição de piso', "Usuário {$authUser->name} abriu o piso '{$piso->Nome}' para edição.");
        }

        return view('ativos.dados_mestres.pisos.edit', compact('piso', 'edificios'));
    }

    /**
     * Atualizar piso existente
     */
    public function update(Request $request, $id)
    {
        $piso = Piso::findOrFail($id);

        $request->validate([
            'Nome' => [
                'required',
                'string',
                'max:100',
                Rule::unique('Pisos')->where(function ($query) use ($request) {
                    return $query->where('EdificioId', $request->EdificioId);
                })->ignore($piso->PisoId, 'PisoId'),
            ],
            'EdificioId' => 'required|exists:Edificios,EdificioId',
        ], [
            'Nome.unique' => 'Já existe um piso com este nome neste edifício.',
        ]);

        $nomeAntigo = $piso->Nome;
        $piso->update([
            'Nome' => $request->Nome,
            'EdificioId' => $request->EdificioId,
        ]);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Atualizou piso', "Usuário {$authUser->name} atualizou o piso de '{$nomeAntigo}' para '{$piso->Nome}' no edifício ID {$piso->EdificioId}.");
        }

        return redirect()->route('dados_mestres.pisos.index')
                         ->with('success', '✅ Piso atualizado com sucesso!');
    }

    /**
     * Excluir piso
     */
    public function destroy($id)
    {
        try {
            $piso = Piso::findOrFail($id);
            $nomePiso = $piso->Nome;
            $piso->delete();

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Excluiu piso', "Usuário {$authUser->name} removeu o piso '{$nomePiso}'.");
            }

            return redirect()->route('dados_mestres.pisos.index')
                             ->with('success', '🗑️ Piso eliminado com sucesso!');
        } catch (Exception $e) {
            if ($authUser = Auth::user()) {
                LogHelper::registrar('Erro ao excluir piso', "Usuário {$authUser->name} tentou excluir o piso ID {$id}, mas ocorreu erro: {$e->getMessage()}.");
            }

            return redirect()->route('dados_mestres.pisos.index')
                             ->with('error', 'Erro ao eliminar o piso. Verifique se ele está associado a outras tabelas.');
        }
    }
}
