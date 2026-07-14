<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provincia;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Exception;

class ProvinciaController extends Controller
{
    /**
     * Lista todas as províncias com paginação.
     */
    public function index()
    {
        $provincias = Provincia::orderBy('Nome')->paginate(5);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou lista de províncias', "Usuário {$authUser->name} acessou a lista de províncias.");
        }

        return view('ativos.dados_mestres.provincias.index', compact('provincias'));
    }

    /**
     * Exibe o formulário de criação de nova província.
     */
    public function create()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou criação de província', "Usuário {$authUser->name} abriu o formulário para criar uma nova província.");
        }

        return view('ativos.dados_mestres.provincias.create');
    }

    /**
     * Armazena uma nova província.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nome' => 'required|string|max:200|unique:Provincias,Nome',
        ]);

        try {
            $provincia = Provincia::create(['Nome' => $request->Nome]);

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Criou província', "Usuário {$authUser->name} criou a província {$provincia->Nome}.");
            }

            return redirect()->route('dados_mestres.provincias.index')
                ->with('success', '✅ Província criada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar província: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário de edição de uma província.
     */
    public function edit($id)
    {
        $provincia = Provincia::findOrFail($id);

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou edição de província', "Usuário {$authUser->name} abriu a província {$provincia->Nome} para edição.");
        }

        return view('ativos.dados_mestres.provincias.edit', compact('provincia'));
    }

    /**
     * Atualiza uma província existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Nome' => 'required|string|max:200|unique:Provincias,Nome,' . $id . ',ProvinciaId',
        ]);

        try {
            $provincia = Provincia::findOrFail($id);
            $provincia->update(['Nome' => $request->Nome]);

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Atualizou província', "Usuário {$authUser->name} atualizou a província {$provincia->Nome}.");
            }

            return redirect()->route('dados_mestres.provincias.index')
                ->with('success', '✅ Província atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar província: ' . $e->getMessage());
        }
    }

    /**
     * Remove uma província.
     */
    public function destroy($id)
    {
        try {
            $provincia = Provincia::findOrFail($id);
            $provincia->delete();

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Excluiu província', "Usuário {$authUser->name} removeu a província {$provincia->Nome}.");
            }

            return redirect()->route('dados_mestres.provincias.index')
                ->with('success', '🗑️ Província eliminada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao eliminar província: ' . $e->getMessage());
        }
    }
}
