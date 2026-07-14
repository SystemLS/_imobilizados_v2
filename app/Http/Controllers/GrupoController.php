<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class GrupoController extends Controller
{
    /**
     * Listagem de grupos com pesquisa e paginação
     */
    public function index(Request $request)
    {
        $query = Grupo::query();

        if ($request->filled('q')) {
            $query->where('Nome', 'LIKE', '%' . $request->q . '%');
        }

        $grupos = $query->orderBy('Nome')->paginate(10);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou lista de grupos', "Usuário {$authUser->name} acessou a lista de grupos.");
        }

        return view('ativos.dados_mestres.grupos.index', compact('grupos'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou criação de grupo', "Usuário {$authUser->name} abriu formulário para criar grupo.");
        }

        return view('ativos.dados_mestres.grupos.create');
    }

    /**
     * Armazena um novo grupo
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nome' => 'required|string|max:150|unique:Grupos,Nome',
        ], [
            'Nome.required' => 'O campo Nome é obrigatório.',
            'Nome.unique' => 'Já existe um grupo com esse nome.',
        ]);

        $grupo = Grupo::create($request->only('Nome'));

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Criou grupo', "Usuário {$authUser->name} criou o grupo {$grupo->Nome}.");
        }

        return redirect()->route('dados_mestres.grupos.index')
                         ->with('success', '✅ Grupo criado com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $grupo = Grupo::findOrFail($id);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou edição de grupo', "Usuário {$authUser->name} abriu grupo {$grupo->Nome} para edição.");
        }

        return view('ativos.dados_mestres.grupos.edit', compact('grupo'));
    }

    /**
     * Atualiza um grupo existente
     */
    public function update(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);

        $request->validate([
            'Nome' => 'required|string|max:150|unique:Grupos,Nome,' . $grupo->GrupoId . ',GrupoId',
        ], [
            'Nome.required' => 'O campo Nome é obrigatório.',
            'Nome.unique' => 'Já existe outro grupo com esse nome.',
        ]);

        $grupo->update($request->only('Nome'));

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Atualizou grupo', "Usuário {$authUser->name} atualizou grupo {$grupo->Nome}.");
        }

        return redirect()->route('dados_mestres.grupos.index')
                         ->with('success', '✅ Grupo atualizado com sucesso!');
    }

    /**
     * Remove um grupo
     */
    public function destroy($id)
    {
        $grupo = Grupo::findOrFail($id);

        try {
            $grupo->delete();

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Excluiu grupo', "Usuário {$authUser->name} removeu grupo {$grupo->Nome}.");
            }

            return redirect()->route('dados_mestres.grupos.index')
                             ->with('success', '🗑️ Grupo eliminado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('dados_mestres.grupos.index')
                             ->with('error', 'Não foi possível eliminar o grupo. Ele pode estar em uso.');
        }
    }

    /**
     * Verifica via AJAX se o nome já existe
     */
    public function verificarNome($nome)
    {
        $existe = Grupo::where('Nome', $nome)->exists();
        return response()->json(['existe' => $existe]);
    }
}
