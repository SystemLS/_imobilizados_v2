<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EstadoConservacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class EstadoConservacaoController extends Controller
{
    /**
     * Listagem de estados de conservação
     */
    public function index()
    {
        $estados = EstadoConservacao::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou lista de estados de conservação', "Usuário {$authUser->name} acessou a lista de estados de conservação.");
        }

        return view('ativos.dados_mestres.estado_conservacao.index', compact('estados'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou criação de estado de conservação', "Usuário {$authUser->name} abriu formulário para criar estado de conservação.");
        }

        return view('ativos.dados_mestres.estado_conservacao.create');
    }

    /**
     * Armazena novo estado de conservação
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nome' => 'required|string|max:100',
            'Descricao' => 'nullable|string|max:255',
        ]);

        $estado = EstadoConservacao::create($request->all());

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Criou estado de conservação', "Usuário {$authUser->name} criou o estado de conservação {$estado->Nome}.");
        }

        return redirect()->route('dados_mestres.estado_conservacao.index')
            ->with('success', '✅ Estado de Conservação criado com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $estado = EstadoConservacao::findOrFail($id);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou edição de estado de conservação', "Usuário {$authUser->name} abriu o estado {$estado->Nome} para edição.");
        }

        return view('ativos.dados_mestres.estado_conservacao.edit', compact('estado'));
    }

    /**
     * Atualiza estado de conservação existente
     */
    public function update(Request $request, $id)
    {
        $estado = EstadoConservacao::findOrFail($id);

        $request->validate([
            'Nome' => 'required|string|max:100',
            'Descricao' => 'nullable|string|max:255',
        ]);

        $estado->update($request->all());

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Atualizou estado de conservação', "Usuário {$authUser->name} atualizou o estado de conservação {$estado->Nome}.");
        }

        return redirect()->route('dados_mestres.estado_conservacao.index')
            ->with('success', '✅ Estado de Conservação atualizado com sucesso!');
    }

    /**
     * Remove um estado de conservação
     */
    public function destroy($id)
    {
        $estado = EstadoConservacao::findOrFail($id);

        try {
            $estado->delete();

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Excluiu estado de conservação', "Usuário {$authUser->name} removeu o estado de conservação {$estado->Nome}.");
            }

            return redirect()->route('dados_mestres.estado_conservacao.index')
                ->with('success', '🗑️ Estado de Conservação removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('dados_mestres.estado_conservacao.index')
                ->with('error', 'Não foi possível remover o estado de conservação. Ele pode estar em uso.');
        }
    }
}
