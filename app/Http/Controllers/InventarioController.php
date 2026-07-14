<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bem;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BensExport;
use Maatwebsite\Excel\Facades\Excel;

class InventarioController extends Controller
{
    public function index(Request $request)
{
    $query = Bem::with(['categoria','grupo','estadoConservacao','sala.piso.edificio.provincia']);

    // Filtro por Província
    if ($request->filled('provincia')) {
        $provinciaId = $request->provincia;
        $query->whereHas('sala.piso.edificio.provincia', function($q) use ($provinciaId) {
            $q->where('ProvinciaId', $provinciaId);
        });
    }

    // Filtro por Edifício
    if ($request->filled('edificio')) {
        $edificioId = $request->edificio;
        $query->whereHas('sala.piso.edificio', function($q) use ($edificioId) {
            $q->where('EdificioId', $edificioId);
        });
    }

    // Filtro por Grupo
    if ($request->filled('grupo')) {
        $query->where('GrupoId', $request->grupo);
    }

    // Filtro por Categoria
    if ($request->filled('categoria')) {
        $query->where('CategoriaId', $request->categoria);
    }

    // Filtro por Estado de Conservação
    if ($request->filled('estado')) {
        $query->where('EstadoConservacaoId', $request->estado);
    }

    $bens = $query->paginate(10)->withQueryString();

    // Para popular selects do filtro
    $provincias = \App\Models\Provincia::all();
    $edificios = \App\Models\Edificio::all();
    $grupos = \App\Models\Grupo::all();
    $categorias = \App\Models\Categoria::all();
    $estados = \App\Models\EstadoConservacao::all();

    return view('inventario.index', compact('bens','provincias','edificios','grupos','categorias','estados'));
}


    public function show(Bem $bem)
    {
        $bem->load(['categoria','grupo','estadoConservacao','sala.piso.edificio.provincia']);
        return view('inventario.show', compact('bem'));
    }

    // ✅ Atualizar o estado de conservação
    public function updateStatus(Request $request, Bem $bem)
{
    $user = Auth::user();
    $perfil = $user->perfil ?? 'usuario';

    // Permissão
    if (!in_array($perfil, ['administrador', 'gestor', 'tecnico_manutencao'])) {
        abort(403, 'Você não tem permissão para atualizar o estado deste ativo.');
    }

    // Validação
    $request->validate([
        'status' => 'required|exists:EstadoConservacao,EstadoConservacaoId',
    ], [
        'status.required' => 'Selecione um estado de conservação válido.',
        'status.exists' => 'O estado de conservação selecionado não existe no sistema.',
    ]);

    // Atualização
    $old = $bem->EstadoConservacaoId;
    $bem->EstadoConservacaoId = $request->status;

    // Força o Eloquent a detectar e salvar a mudança
    if ((int)$old !== (int)$request->status) {
        $bem->save();

        LogHelper::registrar(
            'Inventário: atualizou estado',
            "Usuário {$user->name} ({$perfil}) alterou o estado do ativo {$bem->Nome} ({$bem->getKey()}) de {$old} para {$bem->EstadoConservacaoId}"
        );

        return back()->with('success', 'Estado atualizado com sucesso!');
    }

    return back()->with('info', 'Nenhuma alteração foi feita no estado.');
}


    // ✅ Atualizar informações do ativo com verificação de etiqueta duplicada
    public function update(Request $request, Bem $bem)
    {
$user = Auth::user();
    $perfil = $user->perfil ?? 'usuario';

    // ✅ Verifica se o perfil tem permissão
    if (!in_array($perfil, ['administrador', 'gestor', 'tecnico_manutencao'])) {
        abort(403, 'Você não tem permissão para atualizar este ativo.');
    }

    $allowed = [
        'Nome','Etiqueta','Marca','Modelo','TipoNumeroSerie','NumeroSerieManual','NumeroScanner',
        'Capacidade','Potencia','Descricao','EstadoConservacaoId','CondicaoAmbientalId','preco_aquisicao',
        'valor_depreciado','valor_reavaliado','GrupoId','CategoriaId','PisoId','manutencao','data_aquisicao'
    ];

    $data = $request->only($allowed);

    // 🔍 Descobre o nome da chave primária da tabela
    $primaryKey = $bem->getKeyName();

    // 🔍 Verifica duplicidade de etiqueta
    if (!empty($data['Etiqueta'])) {
        $etiquetaExistente = Bem::where('Etiqueta', $data['Etiqueta'])
            ->where($primaryKey, '<>', $bem->$primaryKey)
            ->first();

        if ($etiquetaExistente) {
            return back()->with('error',
                "⚠️ Valor de etiqueta já existente no sistema. Ativo com este valor: {$etiquetaExistente->Nome}.");
        }
    }

    // ✅ Atualiza os dados
    $bem->update($data);

    LogHelper::registrar(
        'Inventário: atualizou dados',
        "Usuário {$user->name} ({$perfil}) atualizou dados do ativo {$bem->Nome} ({$bem->$primaryKey})"
    );

    return back()->with('success', 'Ativo atualizado com sucesso!');
    }

    // ✅ Exportar PDF
    public function exportPdf(Request $request)
    {
        $titulo = 'Relatório de Inventário — Ativos';
        $logo = public_path('imagens/ENDE.png');
        $data_geracao = now()->format('d/m/Y H:i:s');
        $usuario = auth()->user()->name ?? 'Sistema';

        $bensModels = Bem::with(['grupo','categoria','estadoConservacao','sala.piso.edificio.provincia'])->get();

        // Converte os modelos em arrays para a view
        $bens = $bensModels->map(function($bem) {
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
                    'bom' => 'info',
                    'regular' => 'warning',
                    'ruim', 'péssimo' => 'danger',
                    default => 'secondary'
                }
            ];
        });

        $resumo = [
            'total' => count($bens),
            'grupos' => $bensModels->groupBy('GrupoId')->count(),
            'categorias' => $bensModels->groupBy('CategoriaId')->count(),
            'localizacoes' => $bensModels->groupBy('SalaId')->count(),
        ];

        $descricao = 'Relatório completo de todos os ativos cadastrados no sistema com informações de localização, grupo, categoria e estado de conservação.';

        $pdf = Pdf::loadView('pdf.inventario', compact('bens', 'resumo', 'descricao', 'titulo', 'logo', 'data_geracao', 'usuario'))
                  ->setOptions([
                      'marginTop' => 60,
                      'marginBottom' => 30,
                      'marginLeft' => 15,
                      'marginRight' => 15,
                  ]);

        return $pdf->download('inventario_ativos_' . now()->format('Ymd_His') . '.pdf');
    }

    // ✅ Exportar Excel
    public function exportExcel()
    {
        return Excel::download(new BensExport, 'inventario_ativos.xlsx');
    }
}
