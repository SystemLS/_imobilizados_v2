<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class CategoriaController extends Controller
{
    /**
     * Listagem das categorias com filtros e pesquisa avançada.
     */
    public function index(Request $request)
    {
        $grupoId = $request->input('GrupoId');
        $pesquisa = $request->input('q');
        $grupos = Grupo::orderBy('Nome')->get();
        $query = Categoria::with('grupo');

        if (!empty($grupoId)) {
            $query->where('GrupoId', $grupoId);
        }

        if (!empty($pesquisa)) {
            $query->where(function ($q) use ($pesquisa) {
                $q->where('Nome', 'LIKE', "%{$pesquisa}%")
                  ->orWhereHas('grupo', function ($g) use ($pesquisa) {
                      $g->where('Nome', 'LIKE', "%{$pesquisa}%");
                  });
            });
        }

        $categorias = $query->orderBy('GrupoId')->orderBy('Nome')->paginate(10);

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou lista de categorias',
                'Usuário ' . $authUser->name . ' visualizou a lista de categorias.'
            );
        }

        if ($request->ajax()) {
            return view('ativos.dados_mestres.categorias.index', compact('categorias', 'grupos'))->render();
        }

        return view('ativos.dados_mestres.categorias.index', compact('categorias', 'grupos'));
    }

    /**
     * Formulário de criação de categoria.
     */
    public function create()
    {
        $grupos = Grupo::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou criação de categoria',
                'Usuário ' . $authUser->name . ' abriu o formulário de criação de categoria.'
            );
        }

        return view('ativos.dados_mestres.categorias.create', compact('grupos'));
    }

    /**
     * Armazena nova categoria no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'GrupoId' => 'required|exists:Grupos,GrupoId',
            'Nome' => 'required|string|max:150|unique:Categorias,Nome,NULL,CategoriaId,GrupoId,' . $request->GrupoId,
        ], [
            'GrupoId.required' => 'O campo Grupo é obrigatório.',
            'GrupoId.exists' => 'O grupo selecionado é inválido.',
            'Nome.required' => 'O campo Nome é obrigatório.',
            'Nome.unique' => 'Já existe uma categoria com esse nome neste grupo.',
        ]);

        $categoria = Categoria::create($request->only('GrupoId', 'Nome'));

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Criou categoria',
                'Usuário ' . $authUser->name . ' criou a categoria "' . $categoria->Nome . '" no grupo ID ' . $categoria->GrupoId . '.'
            );
        }

        return redirect()->route('dados_mestres.categorias.index')
                         ->with('success', '✅ Categoria criada com sucesso!');
    }

    /**
     * Formulário de edição de categoria.
     */
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        $grupos = Grupo::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou edição de categoria',
                'Usuário ' . $authUser->name . ' abriu o formulário de edição da categoria "' . $categoria->Nome . '".'
            );
        }

        return view('ativos.dados_mestres.categorias.edit', compact('categoria', 'grupos'));
    }

    /**
     * Atualiza categoria existente.
     */
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'GrupoId' => 'required|exists:Grupos,GrupoId',
            'Nome' => 'required|string|max:150|unique:Categorias,Nome,' . $categoria->CategoriaId . ',CategoriaId,GrupoId,' . $request->GrupoId,
        ], [
            'Nome.required' => 'O campo Nome é obrigatório.',
            'Nome.unique' => 'Já existe uma categoria com esse nome neste grupo.',
        ]);

        $oldData = $categoria->only(['Nome', 'GrupoId']);
        $categoria->update($request->only('GrupoId', 'Nome'));

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Atualizou categoria',
                'Usuário ' . $authUser->name . ' atualizou a categoria "' . $categoria->Nome . '" (Antes: ' . json_encode($oldData) . ', Depois: ' . json_encode($categoria->only(['Nome','GrupoId'])) . ').'
            );
        }

        return redirect()->route('dados_mestres.categorias.index')
                         ->with('success', '✅ Categoria atualizada com sucesso!');
    }

    /**
     * Exclui categoria.
     */
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        $nomeCategoria = $categoria->Nome;
        $categoria->delete();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Deletou categoria',
                'Usuário ' . $authUser->name . ' removeu a categoria "' . $nomeCategoria . '".'
            );
        }

        return redirect()->route('dados_mestres.categorias.index')
                         ->with('success', '🗑️ Categoria eliminada com sucesso!');
    }

    /**
     * Retorna categorias em JSON (para uso AJAX, dropdowns, etc).
     */
    public function porGrupo($grupoId)
    {
        $categorias = Categoria::where('GrupoId', $grupoId)
                               ->orderBy('Nome')
                               ->get(['CategoriaId', 'Nome']);

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Consultou categorias por grupo',
                'Usuário ' . $authUser->name . ' consultou categorias do grupo ID ' . $grupoId . '.'
            );
        }

        return response()->json($categorias);
    }

    /**
     * Verifica duplicidade via AJAX (para validação em tempo real).
     */
    public function verificarDuplicada(Request $request)
    {
        $existe = Categoria::where('Nome', $request->Nome)
            ->where('GrupoId', $request->GrupoId)
            ->exists();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Verificou duplicidade de categoria',
                'Usuário ' . $authUser->name . ' verificou se existe categoria "' . $request->Nome . '" no grupo ID ' . $request->GrupoId . '.'
            );
        }

        return response()->json(['duplicada' => $existe]);
    }
}
