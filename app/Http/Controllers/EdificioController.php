<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Edificio;
use App\Models\Provincia;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Exception;

class EdificioController extends Controller
{
    /**
     * Retorna os edifícios de acordo com a província (JSON para AJAX)
     */
    public function porProvincia($provinciaId)
    {
        $edificios = Edificio::where('ProvinciaId', $provinciaId)
                              ->orderBy('Nome')
                              ->get(['EdificioId', 'Nome']);

        return response()->json($edificios);
    }

    /**
     * Listagem de todos os edifícios
     */
    public function index(Request $request)
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou lista de edifícios',
                'Usuário ' . $authUser->name . ' acessou a lista de edifícios.'
            );
        }

        // Captura os filtros da requisição
        $provinciaId = $request->input('provincia');
        $search = $request->input('search');

        // Consulta inicial com relacionamento
        $query = Edificio::with('provincia')->orderBy('Nome');

        if ($provinciaId) {
            $query->where('ProvinciaId', $provinciaId);
        }

        if ($search) {
            $query->where('Nome', 'like', "%{$search}%");
        }

        $edificios = $query->paginate(10)->withQueryString();
        $provincias = Provincia::orderBy('Nome')->get();

        return view('ativos.dados_mestres.edificios.index', compact('edificios', 'provincias'));
    }

    /**
     * Formulário de criação de novo edifício
     */
    public function create()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Visualizou criação de edifício',
                'Usuário ' . $authUser->name . ' abriu o formulário para criar um novo edifício.'
            );
        }

        $provincias = Provincia::orderBy('Nome')->get();
        return view('ativos.dados_mestres.edificios.create', compact('provincias'));
    }

    /**
     * Armazena um novo edifício
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nome' => 'required|string|max:200|unique:Edificios,Nome',
            'ProvinciaId' => 'required|exists:Provincias,ProvinciaId',
        ]);

        $edificio = Edificio::create($request->only(['Nome','ProvinciaId']));

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Criou edifício',
                'Usuário ' . $authUser->name . ' criou o edifício: ' . $edificio->Nome
            );
        }

        return redirect()->route('dados_mestres.edificios.index')
                         ->with('success', 'Edifício criado com sucesso!');
    }

    /**
     * Formulário de edição do edifício
     */
    public function edit($id)
    {
        $edificio = Edificio::findOrFail($id);

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Visualizou edição de edifício',
                'Usuário ' . $authUser->name . ' abriu o formulário para editar o edifício: ' . $edificio->Nome
            );
        }

        $provincias = Provincia::orderBy('Nome')->get();
        return view('ativos.dados_mestres.edificios.edit', compact('edificio','provincias'));
    }

    /**
     * Atualiza um edifício existente
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Nome' => 'required|string|max:200|unique:Edificios,Nome,' . $id . ',EdificioId',
            'ProvinciaId' => 'required|exists:Provincias,ProvinciaId',
        ]);

        $edificio = Edificio::findOrFail($id);
        $oldData = $edificio->only(['Nome', 'ProvinciaId']);

        $edificio->update($request->only(['Nome','ProvinciaId']));

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Atualizou edifício',
                'Usuário ' . $authUser->name . ' atualizou o edifício ' . $edificio->Nome .
                ' (Antes: ' . json_encode($oldData) . ', Depois: ' . json_encode($edificio->only(['Nome','ProvinciaId'])) . ')'
            );
        }

        return redirect()->route('dados_mestres.edificios.index')
                         ->with('success', 'Edifício atualizado com sucesso!');
    }

    /**
     * Exclui um edifício
     */
    public function destroy($id)
    {
        try {
            $edificio = Edificio::findOrFail($id);
            $edificioName = $edificio->Nome;
            $edificio->delete();

            if ($authUser = Auth::user()) {
                LogHelper::registrar(
                    'Deletou edifício',
                    'Usuário ' . $authUser->name . ' removeu o edifício ' . $edificioName
                );
            }

            return redirect()->route('dados_mestres.edificios.index')
                             ->with('success', 'Edifício eliminado com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('dados_mestres.edificios.index')
                             ->with('error', 'Não foi possível eliminar o edifício. Ele pode estar em uso.');
        }
    }
}
