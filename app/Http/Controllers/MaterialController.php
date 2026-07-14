<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class MaterialController extends Controller
{
    /**
     * Lista de materiais
     */
    public function index()
    {
        $materiais = Material::orderBy('Nome')->paginate(10);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou lista de materiais', "Usuário {$authUser->name} acessou a lista de materiais.");
        }

        return view('ativos.dados_mestres.materiais.index', compact('materiais'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou criação de material', "Usuário {$authUser->name} abriu o formulário para criar material.");
        }

        return view('ativos.dados_mestres.materiais.create');
    }

    /**
     * Salvar novo material
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nome' => 'required|string|max:255|unique:materiais,Nome',
        ]);

        $material = Material::create($request->all());

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Criou material', "Usuário {$authUser->name} criou o material {$material->Nome}.");
        }

        return redirect()
            ->route('dados_mestres.materiais.index')
            ->with('success', '✅ Material criado com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $material = Material::findOrFail($id);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou edição de material', "Usuário {$authUser->name} abriu o material {$material->Nome} para edição.");
        }

        return view('ativos.dados_mestres.materiais.edit', compact('material'));
    }

    /**
     * Atualizar material
     */
    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $request->validate([
            'Nome' => 'required|string|max:255|unique:materiais,Nome,' . $id . ',MaterialId',
        ]);

        $material->update($request->all());

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Atualizou material', "Usuário {$authUser->name} atualizou o material {$material->Nome}.");
        }

        return redirect()
            ->route('dados_mestres.materiais.index')
            ->with('success', '✅ Material atualizado com sucesso!');
    }

    /**
     * Excluir material
     */
    public function destroy($id)
    {
        $material = Material::findOrFail($id);

        try {
            $material->delete();

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Excluiu material', "Usuário {$authUser->name} removeu o material {$material->Nome}.");
            }

            return redirect()
                ->route('dados_mestres.materiais.index')
                ->with('success', '🗑️ Material removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('dados_mestres.materiais.index')
                ->with('error', 'Não foi possível remover o material. Ele pode estar em uso.');
        }
    }
}
