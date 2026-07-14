<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bem;
use App\Models\Provincia;
use App\Models\Grupo;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\EstadoConservacao;
use App\Models\CondicaoAmbiental;
use App\Models\Sala;
use App\Models\Edificio;
use App\Models\Material;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Exception;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AtivosExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\CurrencyHelper;

class BemController extends Controller
{
    // -----------------------------
    // LISTAGEM DE ATIVOS
    // -----------------------------
    public function index(Request $request)
    {
        $query = Bem::query()->with(['subcategoria', 'sala', 'estadoConservacao']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Nome', 'like', "%{$search}%")
                  ->orWhere('Etiqueta', 'like', "%{$search}%");
            });
        }

        if ($request->filled('edificio_id')) {
            $query->whereHas('sala.piso.edificio', function($q) use ($request) {
                $q->where('EdificioId', $request->edificio_id);
            });
        }

        if ($request->filled('sala_id')) {
            $query->where('SalaId', $request->sala_id);
        }

        if ($request->ordem_tempo === 'mais_recente') {
            $query->orderBy('created_at', 'desc');
        } elseif ($request->ordem_tempo === 'mais_antigo') {
            $query->orderBy('created_at', 'asc');
        }

        if ($request->ordem_nome === 'asc') {
            $query->orderBy('Nome', 'asc');
        } elseif ($request->ordem_nome === 'desc') {
            $query->orderBy('Nome', 'desc');
        }

        $ativos = $query->paginate(10);
        $edificios = Edificio::all();
        $salas = Sala::all();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou a lista de ativos',
                'Usuário ' . $authUser->name . ' visualizou a lista de ativos.'
            );
        }

        return view('ativos.index', compact('ativos', 'edificios', 'salas'));
    }

    // -----------------------------
    // EXIBIR DETALHES DO ATIVO
    // -----------------------------
    public function show(Bem $bem)
    {
        $bem->load([
            'subcategoria',
            'sala.piso.edificio.provincia',
            'estadoConservacao',
            'condicaoAmbiental',
            'materiais',
            'manutencoes',
            'reavaliacoes'
        ]);

        $fotosPrincipais = collect([$bem->Foto1, $bem->Foto2, $bem->Foto3])->filter();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Visualizou ativo',
                'Usuário ' . $authUser->name . ' visualizou o ativo "' . $bem->Nome . '".'
            );
        }

        return view('ativos.show', compact('bem', 'fotosPrincipais'));
    }

    // -----------------------------
    // FORMULÁRIO DE CRIAÇÃO
    // -----------------------------
    public function create()
    {
        $provincias   = Provincia::orderBy('Nome')->get();
        $grupos       = Grupo::orderBy('Nome')->get();
        $categorias   = Categoria::orderBy('Nome')->get();
        $subcategorias = Subcategoria::orderBy('Nome')->get();
        $estados      = EstadoConservacao::orderBy('Nome')->get();
        $condicoes    = CondicaoAmbiental::orderBy('Nome')->get();
        $salas        = Sala::orderBy('Nome')->get();
        $materiais    = Material::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou criação de ativo',
                'Usuário ' . $authUser->name . ' abriu o formulário de criação de ativo.'
            );
        }

        return view('ativos.create', compact(
            'provincias','grupos','categorias','subcategorias',
            'estados','condicoes','salas','materiais'
        ));
    }

    // -----------------------------
    // CRIAR ATIVO
    // -----------------------------
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ProvinciaId' => 'required|integer',
                'EdificioId' => 'required|integer',
                'PisoId' => 'required|integer',
                'SalaId' => 'required|integer',
                'GrupoId' => 'required|integer',
                'CategoriaId' => 'required|integer',
                'SubcategoriaId' => 'nullable|integer',
                'Nome' => 'required|string|max:255',
                'Etiqueta' => ['required','string','max:8','unique:bens,Etiqueta'],
                'Marca' => 'required|string|max:255',
                'Modelo' => 'required|string|max:255',
                'TipoNumeroSerie' => 'required|in:NumeroSerieManual,NumeroScanner',
                'NumeroSerieManual' => 'nullable|string|max:255',
                'NumeroScanner' => 'nullable|string|max:255',
                'Capacidade' => 'required|string|max:255',
                'Potencia' => 'nullable|string|max:255',
                'Descricao' => 'required|string',
                'EstadoConservacaoId' => 'required|integer',
                'CondicaoAmbientalId' => 'required|integer',
                'preco_aquisicao' => 'required|numeric|min:0',
                'data_aquisicao' => 'required|date',
                'Foto1' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'Foto2' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'Foto3' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'Materiais' => 'nullable|array',
                'Materiais.*' => 'integer|exists:materiais,MaterialId',
            ]);

            foreach (['Foto1','Foto2','Foto3'] as $foto) {
                if ($request->hasFile($foto)) {
                    $validated[$foto] = $request->file($foto)->store('bens','public');
                }
            }

            $bem = Bem::create($validated);

            if ($request->has('Materiais')) {
                $bem->materiais()->sync($request->Materiais);
            }

            if ($authUser = Auth::user()) {
                LogHelper::registrar(
                    'Criou ativo',
                    'Usuário ' . $authUser->name . ' criou o ativo "' . $bem->Nome . '".'
                );
            }

            return redirect()->route('ativos.index')->with('success','Ativo cadastrado com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar ativo: ' . $e->getMessage());
        }
    }

    // -----------------------------
    // FORMULÁRIO DE EDIÇÃO
    // -----------------------------
    public function edit(Bem $bem)
    {
        $provincias   = Provincia::orderBy('Nome')->get();
        $grupos       = Grupo::orderBy('Nome')->get();
        $categorias   = Categoria::orderBy('Nome')->get();
        $subcategorias = Subcategoria::orderBy('Nome')->get();
        $estados      = EstadoConservacao::orderBy('Nome')->get();
        $condicoes    = CondicaoAmbiental::orderBy('Nome')->get();
        $salas        = Sala::orderBy('Nome')->get();
        $materiais    = Material::orderBy('Nome')->get();

        $bem->load('materiais');

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou edição de ativo',
                'Usuário ' . $authUser->name . ' abriu o formulário de edição do ativo "' . $bem->Nome . '".'
            );
        }

        return view('ativos.edit', compact(
            'bem','provincias','grupos','categorias','subcategorias',
            'estados','condicoes','salas','materiais'
        ));
    }

    // -----------------------------
    // ATUALIZAR ATIVO
    // -----------------------------
    public function update(Request $request, Bem $bem)
    {
        $rules = [
            'ProvinciaId' => 'required|integer',
            'EdificioId' => 'required|integer',
            'PisoId' => 'required|integer',
            'SalaId' => 'required|integer',
            'GrupoId' => 'required|integer',
            'CategoriaId' => 'required|integer',
            'SubcategoriaId' => 'nullable|integer',
            'Nome' => 'required|string|max:255',
            'Etiqueta' => ['nullable','string','max:8',Rule::unique('bens','Etiqueta')->ignore($bem->BemId,'BemId')],
            'Marca' => 'nullable|string|max:255',
            'Modelo' => 'nullable|string|max:255',
            'TipoNumeroSerie' => 'required|in:NumeroSerieManual,NumeroScanner',
            'NumeroSerieManual' => 'nullable|string|max:255',
            'NumeroScanner' => 'nullable|string|max:255',
            'Capacidade' => 'required|string|max:255',
            'Potencia' => 'nullable|string|max:255|required_if:GrupoId,1,2',
            'Descricao' => 'nullable|string',
            'EstadoConservacaoId' => 'required|integer',
            'CondicaoAmbientalId' => 'required|integer',
            'preco_aquisicao' => 'nullable|numeric|min:0',
            'data_aquisicao' => 'nullable|date',
            'Foto1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'Foto2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'Foto3' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'Materiais' => 'nullable|array',
            'Materiais.*' => 'integer|exists:materiais,MaterialId',
        ];

        $oldData = $bem->only([
            'Nome','Etiqueta','Marca','Modelo','TipoNumeroSerie',
            'NumeroSerieManual','NumeroScanner','Capacidade','Potencia','Descricao',
            'EstadoConservacaoId','CondicaoAmbientalId','preco_aquisicao','data_aquisicao'
        ]);

        $data = $request->validate($rules);

        foreach (['Foto1','Foto2','Foto3'] as $foto) {
            if ($request->hasFile($foto)) {
                if ($bem->$foto) Storage::disk('public')->delete($bem->$foto);
                $data[$foto] = $request->file($foto)->store('bens','public');
            }
        }

        $bem->update($data);
        $bem->materiais()->sync($request->Materiais ?? []);

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Atualizou ativo',
                'Usuário ' . $authUser->name . ' atualizou o ativo "' . $bem->Nome . '" (Antes: ' . json_encode($oldData) . ', Depois: ' . json_encode($bem->only(array_keys($data))) . ')'
            );
        }

        return redirect()->route('ativos.show',$bem->BemId)
                         ->with('success','Ativo atualizado com sucesso!');
    }

    // -----------------------------
    // EXCLUIR ATIVO
    // -----------------------------
    public function destroy($id)
    {
        $ativo = Bem::findOrFail($id);
        $nomeAtivo = $ativo->Nome;
        $ativo->delete();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Deletou ativo',
                'Usuário ' . $authUser->name . ' excluiu o ativo "' . $nomeAtivo . '".'
            );
        }

        return redirect()->route('ativos.index')->with('success','Ativo excluído com sucesso!');
    }

    public function verificarEtiqueta($etiqueta)
    {
        $existe = Bem::where('Etiqueta', $etiqueta)->exists();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Verificou etiqueta',
                'Usuário ' . $authUser->name . ' verificou a existência da etiqueta "' . $etiqueta . '".'
            );
        }

        return response()->json(['exists' => $existe]);
    }

    /**
     * Exporta ativos em PDF
     */
    public function exportPdf()
    {
        $titulo = 'Inventário de Ativos — Completo';
        $logo = public_path('imagens/ENDE.png');
        $data_geracao = now()->format('d/m/Y H:i:s');
        $usuario = auth()->user()->name ?? 'Sistema';

        $ativos_models = Bem::with(['grupo','categoria','estadoConservacao','sala.piso.edificio.provincia'])->get();

        // Formata dados para a view
        $ativos = $ativos_models->map(function($bem) {
            return [
                'etiqueta' => $bem->Etiqueta ?? '-',
                'nome' => $bem->Nome,
                'grupo' => $bem->grupo->Nome ?? '-',
                'categoria' => $bem->categoria->Nome ?? '-',
                'localizacao' => optional($bem->sala->piso->edificio->provincia)->Nome ?? '-' . ' / ' .
                               optional($bem->sala->piso->edificio)->Nome ?? '-' . ' / ' .
                               optional($bem->sala->piso)->Nome ?? '-' . ' / ' .
                               optional($bem->sala)->Nome ?? '-',
                'estado' => $bem->estadoConservacao->Nome ?? '-',
                'estado_classe' => match(strtolower($bem->estadoConservacao->Nome ?? '')) {
                    'excelente', 'ótimo' => 'success',
                    'bom' => 'success',
                    'regular' => 'warning',
                    'ruim', 'péssimo' => 'danger',
                    default => 'success'
                },
                'preco' => CurrencyHelper::formatKz($bem->preco_aquisicao ?? 0, 2, false),
            ];
        });

        $resumo = [
            'total' => $ativos_models->count(),
            'grupos' => $ativos_models->groupBy('GrupoId')->count(),
            'categorias' => $ativos_models->groupBy('CategoriaId')->count(),
            'valor_total' => CurrencyHelper::formatKz($ativos_models->sum('preco_aquisicao') ?? 0),
        ];

        $descricao = 'Inventário completo de todos os ativos do sistema com dados de localização, categorização, estado e valores em Kwanzas (Kz).';

        if ($user = Auth::user()) {
            LogHelper::registrar('Exportou ativos PDF', "Usuário {$user->name} exportou inventário de ativos em PDF.");
        }

        $pdf = Pdf::loadView('pdf.ativos', compact('ativos', 'resumo', 'descricao', 'titulo', 'logo', 'data_geracao', 'usuario'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'marginTop' => 60,
                'marginBottom' => 30,
                'marginLeft' => 15,
                'marginRight' => 15,
            ]);

        return $pdf->download('ativos_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Exporta ativos em Excel
     */
    public function exportExcel()
    {
        if ($user = Auth::user()) {
            LogHelper::registrar('Exportou ativos Excel', "Usuário {$user->name} exportou inventário de ativos em Excel.");
        }

        return Excel::download(new AtivosExport(), 'ativos_' . now()->format('Ymd_His') . '.xlsx');
    }
}
