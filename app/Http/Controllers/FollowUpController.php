<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    FollowUp,
    FollowUpItem,
    Sala,
    Piso,
    Edificio,
    Provincia,
    Bem
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FollowUpExport;

class FollowUpController extends Controller
{
    /* =======================
     *  PÁGINA INICIAL
     * ======================= */
    public function index()
    {
        $provincias = Provincia::orderBy('Nome')
            ->get(['ProvinciaId', 'Nome']);

        LogHelper::registrar(
            'Acesso FollowUp',
            'Usuário ' . Auth::user()->name . ' acessou a página principal de FollowUp.'
        );

        return view('followup.index', compact('provincias'));
    }

    /* =======================
     *  FILTROS EM CASCATA
     * ======================= */
    public function edificiosByProvincia($provinciaId)
    {
        $edificios = Edificio::where('ProvinciaId', $provinciaId)
            ->orderBy('Nome')
            ->get(['EdificioId as id', 'Nome']);

        LogHelper::registrar(
            'Filtro FollowUp',
            "Usuário " . Auth::user()->name . " filtrou edifícios da província ID {$provinciaId}."
        );

        return response()->json($edificios);
    }

    public function pisosByEdificio($edificioId)
    {
        $pisos = Piso::where('EdificioId', $edificioId)
            ->orderBy('Nome')
            ->get(['PisoId as id', 'Nome']);

        LogHelper::registrar(
            'Filtro FollowUp',
            "Usuário " . Auth::user()->name . " filtrou pisos do edifício ID {$edificioId}."
        );

        return response()->json($pisos);
    }

    public function salasByPiso($pisoId)
    {
        $salas = Sala::where('PisoId', $pisoId)
            ->orderBy('Nome')
            ->get(['SalaId as id', 'Nome']);

        LogHelper::registrar(
            'Filtro FollowUp',
            "Usuário " . Auth::user()->name . " filtrou salas do piso ID {$pisoId}."
        );

        return response()->json($salas);
    }

    /* =======================
     *  BENS DA SALA
     * ======================= */
    public function bensBySala($salaId)
    {
        $sala = Sala::find($salaId);

        if (!$sala) {
            LogHelper::registrar(
                'Erro FollowUp',
                "Usuário " . Auth::user()->name . " tentou acessar bens de uma sala inexistente ID {$salaId}."
            );
            return response()->json([], 404);
        }

        $bens = Bem::with('estadoConservacao')
            ->where('SalaId', $salaId)
            ->orderBy('Nome')
            ->get()
            ->map(fn ($bem) => [
                'id' => $bem->BemId,
                'Etiqueta' => $bem->Etiqueta,
                'Nome' => $bem->Nome,
                'estadoConservacao' => $bem->estadoConservacao->Nome ?? '-'
            ]);

        LogHelper::registrar(
            'Consulta FollowUp',
            "Usuário " . Auth::user()->name . " listou bens da sala ID {$salaId}."
        );

        return response()->json($bens);
    }

    /* =======================
     *  SUBMISSÃO DO FOLLOW-UP
     * ======================= */
    public function submit(Request $request)
    {
        $request->validate([
            'sala_id' => 'required|exists:salas,SalaId',
            'bens' => 'required|array',
            'bens.*.id' => 'required|exists:bens,BemId',
            'bens.*.presente' => 'required|boolean',
            'observacao' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $sala = Sala::with('piso.edificio.provincia')
                ->findOrFail($request->sala_id);

            /* ===== FOLLOW UP (CABECALHO) ===== */
            $follow = FollowUp::create([
                'sala_id'      => $sala->SalaId,
                'piso_id'      => $sala->PisoId,
                'edificio_id'  => $sala->piso->EdificioId,
                'provincia_id' => $sala->piso->edificio->ProvinciaId,
                'usuario_id'   => Auth::id(),
                'iniciado_em'  => now(),
                'finalizado_em'=> now(),
                'status'       => 'finalizado',
                'observacoes'  => $request->observacao
            ]);

            $ativosEncontrados = 0;
            $relatorio = [];

            /* ===== ITENS ===== */
            foreach ($request->bens as $bemData) {
                $bem = Bem::with('estadoConservacao')
                    ->findOrFail($bemData['id']);

                $presente = $bemData['presente'] ? 1 : 0;
                $ativosEncontrados += $presente;

                FollowUpItem::create([
                    'follow_up_id' => $follow->id,
                    'bem_id'       => $bem->BemId,
                    'sala_id'      => $sala->SalaId,
                    'piso_id'      => $sala->PisoId,
                    'edificio_id'  => $sala->piso->EdificioId,
                    'provincia_id' => $sala->piso->edificio->ProvinciaId,
                    'etiqueta'     => $bem->Etiqueta,
                    'nome'         => $bem->Nome,
                    'presente'     => $presente,
                    'estado'       => $bem->estadoConservacao->Nome ?? '-',
                    'observacao'   => $request->observacao
                ]);

                $relatorio[] = [
                    'bem_id'       => $bem->BemId,
                    'etiqueta'     => $bem->Etiqueta,
                    'nome'         => $bem->Nome,
                    'presente'     => $presente,
                    'estado'       => $bem->estadoConservacao->Nome ?? '-',
                    'observacao'   => $request->observacao,
                    'sala_id'      => $sala->SalaId,
                    'piso_id'      => $sala->PisoId,
                    'edificio_id'  => $sala->piso->EdificioId,
                    'provincia_id' => $sala->piso->edificio->ProvinciaId
                ];
            }

            $ativosNaoEncontrados = count($request->bens) - $ativosEncontrados;

            $follow->update([
                'ativos_encontrados' => $ativosEncontrados,
                'ativos_nao_encontrados' => $ativosNaoEncontrados,
                'relatorio_json' => json_encode($relatorio, JSON_UNESCAPED_UNICODE)
            ]);

            LogHelper::registrar(
                'FollowUp Submetido',
                "Usuário " . Auth::user()->name .
                " finalizou followup {$follow->id} com {$ativosEncontrados} ativos encontrados e {$ativosNaoEncontrados} não encontrados."
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'followup_id' => $follow->id
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            LogHelper::registrar(
                'Erro FollowUp',
                "Usuário " . Auth::user()->name . " encontrou erro ao enviar followup: " . $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /* =======================
     *  EXPORTAÇÃO
     * ======================= */
    public function export($id, $tipo)
    {
        $followUp = FollowUp::with([
            'sala',
            'piso',
            'edificio',
            'provincia',
            'usuario',
            'itens.bem'
        ])->findOrFail($id);

        LogHelper::registrar(
            'Exportação FollowUp',
            "Usuário " . Auth::user()->name . " exportou followup ID {$id} como {$tipo}."
        );

        $fileName = 'FollowUp_' . $followUp->id . '_' . now()->format('Ymd_His');

        if ($tipo === 'pdf') {
            $titulo = 'Relatório de Follow-Up — Acompanhamento';
            $logo = public_path('imagens/ENDE.png');
            $data_geracao = now()->format('d/m/Y H:i:s');
            $usuario = auth()->user()->name ?? 'Sistema';

            $pdf = Pdf::loadView('followup.pdf', compact('followUp', 'titulo', 'logo', 'data_geracao', 'usuario'))
                ->setOptions([
                    'marginTop' => 60,
                    'marginBottom' => 30,
                    'marginLeft' => 15,
                    'marginRight' => 15,
                ]);

            return $pdf->download("{$fileName}.pdf");
        }

        if ($tipo === 'excel') {
            return Excel::download(
                new FollowUpExport($followUp),
                "{$fileName}.xlsx"
            );
        }

        return back()->with('error', 'Tipo de exportação inválido!');
    }

    /* =======================
     *  RELATÓRIOS
     * ======================= */
    public function relatorios()
    {
        try {
            $follows = FollowUp::with([
                'sala',
                'piso',
                'edificio',
                'provincia',
                'usuario'
            ])
            ->orderByDesc('finalizado_em')
            ->orderByDesc('iniciado_em')
            ->paginate(20);

            LogHelper::registrar(
                'Relatórios FollowUp',
                "Usuário " . Auth::user()->name . " acessou a página de relatórios de followups."
            );

            return view('followup.relatorioFollow', compact('follows'));

        } catch (\Exception $e) {
            LogHelper::registrar(
                'Erro Relatórios FollowUp',
                'Erro ao carregar relatórios: ' . $e->getMessage()
            );

            return back()->with('error', 'Erro ao carregar relatórios.');
        }
    }

    /* =======================
     *  COMPARAÇÃO
     * ======================= */
    public function comparacao($id)
    {
        $followup = FollowUp::with([
            'sala',
            'piso',
            'edificio',
            'provincia',
            'usuario',
            'itens.bem'
        ])->findOrFail($id);

        LogHelper::registrar(
            'Comparação FollowUp',
            "Usuário " . Auth::user()->name . " visualizou comparação detalhada do followup ID {$id}."
        );

        return view('followup.comparacao', compact('followup'));
    }
}
