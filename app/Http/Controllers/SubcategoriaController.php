<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subcategoria;
use App\Models\Categoria;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class SubcategoriaController extends Controller
{
    /**
     * Lista de subcategorias
     */
    public function index(Request $request)
    {
        $grupoId = $request->input('GrupoId');
        $categoriaId = $request->input('CategoriaId');
        $pesquisa = $request->input('q');

        $grupos = Grupo::orderBy('Nome')->get();
        $categorias = Categoria::orderBy('Nome')->get();

        $query = Subcategoria::with(['categoria.grupo']);

        if (!empty($grupoId)) {
            $query->whereHas('categoria', function ($q) use ($grupoId) {
                $q->where('GrupoId', $grupoId);
            });
        }

        if (!empty($categoriaId)) {
            $query->where('CategoriaId', $categoriaId);
        }

        if (!empty($pesquisa)) {
            $query->where('Nome', 'LIKE', "%{$pesquisa}%");
        }

        $subcategorias = $query->orderBy('Nome')->paginate(10);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou lista de subcategorias', "Usuário {$authUser->name} acessou a lista de subcategorias.");
        }

        return view('ativos.dados_mestres.subcategorias.index', compact('grupos', 'categorias', 'subcategorias'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $grupos = Grupo::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou criação de subcategoria', "Usuário {$authUser->name} abriu o formulário para criar subcategoria.");
        }

        return view('ativos.dados_mestres.subcategorias.create', compact('grupos'));
    }

    /**
     * Salvar nova subcategoria
     */
    public function store(Request $request)
    {
        $request->validate([
            'CategoriaId' => 'required|exists:Categorias,CategoriaId',
            'Nome' => 'required|string|max:150|unique:Subcategorias,Nome,NULL,SubcategoriaId,CategoriaId,' . $request->CategoriaId,
        ], [
            'CategoriaId.required' => 'Selecione uma categoria.',
            'Nome.required' => 'O nome é obrigatório.',
            'Nome.unique' => 'Já existe uma subcategoria com esse nome nesta categoria.',
        ]);

        $subcategoria = Subcategoria::create($request->only('CategoriaId', 'Nome'));

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Criou subcategoria', "Usuário {$authUser->name} criou a subcategoria {$subcategoria->Nome}.");
        }

        return redirect()->route('dados_mestres.subcategorias.index')
                         ->with('success', '✅ Subcategoria criada com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $subcategoria = Subcategoria::findOrFail($id);
        $grupos = Grupo::orderBy('Nome')->get();
        $categorias = Categoria::where('GrupoId', $subcategoria->categoria->GrupoId)
                               ->orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou edição de subcategoria', "Usuário {$authUser->name} abriu a subcategoria {$subcategoria->Nome} para edição.");
        }

        return view('ativos.dados_mestres.subcategorias.edit', compact('subcategoria', 'grupos', 'categorias'));
    }

    /**
     * Atualizar subcategoria
     */
    public function update(Request $request, $id)
    {
        $subcategoria = Subcategoria::findOrFail($id);

        $request->validate([
            'CategoriaId' => 'required|exists:Categorias,CategoriaId',
            'Nome' => 'required|string|max:150|unique:Subcategorias,Nome,' . $subcategoria->SubcategoriaId . ',SubcategoriaId,CategoriaId,' . $request->CategoriaId,
        ]);

        $subcategoria->update($request->only('CategoriaId', 'Nome'));

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Atualizou subcategoria', "Usuário {$authUser->name} atualizou a subcategoria {$subcategoria->Nome}.");
        }

        return redirect()->route('dados_mestres.subcategorias.index')
                         ->with('success', '✅ Subcategoria atualizada com sucesso!');
    }

    /**
     * Excluir subcategoria
     */
    public function destroy($id)
    {
        $subcategoria = Subcategoria::findOrFail($id);

        try {
            $subcategoria->delete();

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Excluiu subcategoria', "Usuário {$authUser->name} removeu a subcategoria {$subcategoria->Nome}.");
            }

            return redirect()->route('dados_mestres.subcategorias.index')
                             ->with('success', '🗑️ Subcategoria eliminada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('dados_mestres.subcategorias.index')
                             ->with('error', 'Não foi possível remover a subcategoria. Ela pode estar em uso.');
        }
    }

    /**
     * 🔹 AJAX: listar subcategorias por categoria
     */
    public function subcategoriasPorCategoria($categoriaId)
    {
        $subcategorias = Subcategoria::where('CategoriaId', $categoriaId)
                                     ->orderBy('Nome')
                                     ->get(['SubcategoriaId', 'Nome']);
        return response()->json($subcategorias);
    }
}
