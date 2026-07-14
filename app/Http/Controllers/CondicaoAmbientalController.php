<?php

namespace App\Http\Controllers;

use App\Models\CondicaoAmbiental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class CondicaoAmbientalController extends Controller
{
    /**
     * Lista de condições ambientais
     */
    public function index()
    {
        $condicoes = CondicaoAmbiental::orderBy('Nome')->paginate(10);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou lista de condições ambientais', "Usuário {$authUser->name} acessou a lista de condições ambientais.");
        }

        return view('ativos.dados_mestres.condicoes_ambientais.index', compact('condicoes'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou criação de condição ambiental', "Usuário {$authUser->name} abriu o formulário para criar condição ambiental.");
        }

        return view('ativos.dados_mestres.condicoes_ambientais.create');
    }

    /**
     * Salvar nova condição ambiental
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nome' => 'required|string|max:255|unique:CondicoesAmbientais,Nome',
            'Descricao' => 'nullable|string|max:500',
        ]);

        $condicao = CondicaoAmbiental::create($request->all());

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Criou condição ambiental', "Usuário {$authUser->name} criou a condição ambiental {$condicao->Nome}.");
        }

        return redirect()
            ->route('dados_mestres.condicoes_ambientais.index')
            ->with('success', '✅ Condição ambiental criada com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $condicao = CondicaoAmbiental::findOrFail($id);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou edição de condição ambiental', "Usuário {$authUser->name} abriu a condição {$condicao->Nome} para edição.");
        }

        return view('ativos.dados_mestres.condicoes_ambientais.edit', compact('condicao'));
    }

    /**
     * Atualizar condição ambiental
     */
    public function update(Request $request, $id)
    {
        $condicao = CondicaoAmbiental::findOrFail($id);

        $request->validate([
            'Nome' => 'required|string|max:255|unique:CondicoesAmbientais,Nome,' . $id . ',CondicaoAmbientalId',
            'Descricao' => 'nullable|string|max:500',
        ]);

        $condicao->update($request->all());

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Atualizou condição ambiental', "Usuário {$authUser->name} atualizou a condição {$condicao->Nome}.");
        }

        return redirect()
            ->route('dados_mestres.condicoes_ambientais.index')
            ->with('success', '✅ Condição ambiental atualizada com sucesso!');
    }

    /**
     * Excluir condição ambiental
     */
    public function destroy($id)
    {
        $condicao = CondicaoAmbiental::findOrFail($id);

        try {
            $condicao->delete();

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Excluiu condição ambiental', "Usuário {$authUser->name} removeu a condição {$condicao->Nome}.");
            }

            return redirect()
                ->route('dados_mestres.condicoes_ambientais.index')
                ->with('success', '🗑️ Condição ambiental removida com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('dados_mestres.condicoes_ambientais.index')
                ->with('error', 'Não foi possível remover a condição ambiental. Ela pode estar em uso.');
        }
    }
}
