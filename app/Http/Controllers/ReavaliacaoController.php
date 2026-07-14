<?php

namespace App\Http\Controllers;

use App\Models\Reavaliacao;
use App\Models\Bem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReavaliacoesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use App\Helpers\CurrencyHelper;

class ReavaliacaoController extends Controller
{
    public function index()
    {
        $query = Reavaliacao::with(['bem', 'usuario']);

        if (request()->filled('etiqueta')) {
            $etiqueta = request('etiqueta');
            $query->whereHas('bem', function ($q) use ($etiqueta) {
                $q->where('Etiqueta', 'like', "%{$etiqueta}%");
            });
        }

        if (request()->filled('valor_inicial_min')) {
            $query->where('valor_inicial', '>=', request('valor_inicial_min'));
        }

        if (request()->filled('valor_inicial_max')) {
            $query->where('valor_inicial', '<=', request('valor_inicial_max'));
        }

        if (request()->filled('data_reavaliacao')) {
            $query->whereDate('data_reavaliacao', request('data_reavaliacao'));
        }

        $reavaliacoes = $query
            ->orderByDesc('data_reavaliacao')
            ->paginate(15)
            ->withQueryString();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou lista de reavaliações',
                "Usuário {$authUser->name} acedeu à lista de reavaliações."
            );
        }

        return view('reavaliacoes.index', compact('reavaliacoes'));
    }

    public function create()
    {
        $bens = Bem::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou criação de reavaliação',
                "Usuário {$authUser->name} abriu o formulário de criação de reavaliação."
            );
        }

        return view('reavaliacoes.create', compact('bens'));
    }

    public function store(Request $request)
    {
        // Compatibilidade com formulários antigos que enviam vida_util_restante.
        $request->merge([
            'vida_util' => $request->input('vida_util', $request->input('vida_util_restante')),
        ]);

        $request->validate([
            'bem_id'            => 'required|exists:bens,BemId',
            'valor_inicial'     => 'required|numeric|min:0',
            'taxa_depreciacao'  => 'nullable|numeric|min:0|max:100',
            'vida_util'         => 'nullable|integer|min:1',
            'data_aquisicao'    => 'required|date',
            'vlc'               => 'nullable|numeric',
            'nova_depreciacao_anual' => 'nullable|numeric',
            'valor_residual'    => 'nullable|numeric|min:0',
            'valor_atualizado'  => 'required|numeric|min:0',
            'data_reavaliacao'  => 'required|date',
            'observacoes'       => 'nullable|string',
        ]);

        $reavaliacao = Reavaliacao::create([
            'bem_id'           => $request->bem_id,
            'usuario_id'       => Auth::id(),
            'valor_inicial'    => $request->valor_inicial,
            'taxa_depreciacao' => $request->taxa_depreciacao,
            'vida_util'        => $request->vida_util,
            'data_aquisicao'   => Carbon::parse($request->data_aquisicao)->format('Y-m-d'),
            'vlc'              => $request->vlc,
            'nova_depreciacao_anual' => $request->nova_depreciacao_anual,
            'valor_residual'   => $request->valor_residual,
            'valor_atualizado' => $request->valor_atualizado,
            'data_reavaliacao' => Carbon::parse($request->data_reavaliacao)->format('Y-m-d'),
            'metodo'           => 'Linear',
            'observacoes'      => $request->observacoes,
        ]);

        // Actualiza o valor reavaliado no bem
        $bem = Bem::find($request->bem_id);
        if ($bem) {
            $bem->valor_reavaliado = $request->valor_atualizado;
            $bem->save();
        }

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Criou reavaliação',
                "Usuário {$authUser->name} registou uma reavaliação para o bem {$bem->Nome}."
            );
        }

        return redirect()
            ->route('reavaliacoes.index')
            ->with('success', '✅ Reavaliação registada com sucesso!');
    }

    public function show($id)
    {
        $reavaliacao = Reavaliacao::with(['bem', 'usuario'])->findOrFail($id);

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Visualizou reavaliação',
                "Usuário {$authUser->name} visualizou a reavaliação ID {$id}."
            );
        }

        return view('reavaliacoes.show', compact('reavaliacao'));
    }

    public function edit($id)
    {
        $reavaliacao = Reavaliacao::with('bem')->findOrFail($id);
        $bens = Bem::orderBy('Nome')->get();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou edição de reavaliação',
                "Usuário {$authUser->name} abriu a reavaliação ID {$id} para edição."
            );
        }

        return view('reavaliacoes.edit', compact('reavaliacao', 'bens'));
    }

    public function update(Request $request, $id)
    {
        // Compatibilidade com formulários antigos que enviam vida_util_restante.
        $request->merge([
            'vida_util' => $request->input('vida_util', $request->input('vida_util_restante')),
        ]);

        $request->validate([
            'valor_inicial'     => 'required|numeric|min:0',
            'taxa_depreciacao'  => 'nullable|numeric|min:0|max:100',
            'vida_util'         => 'nullable|integer|min:1',
            'data_aquisicao'    => 'required|date',
            'vlc'               => 'nullable|numeric',
            'nova_depreciacao_anual' => 'nullable|numeric',
            'valor_residual'    => 'nullable|numeric|min:0',
            'valor_atualizado'  => 'required|numeric|min:0',
            'data_reavaliacao'  => 'required|date',
            'observacoes'       => 'nullable|string',
        ]);

        $reavaliacao = Reavaliacao::findOrFail($id);

        $reavaliacao->update([
            'valor_inicial'    => $request->valor_inicial,
            'taxa_depreciacao' => $request->taxa_depreciacao,
            'vida_util'        => $request->vida_util,
            'data_aquisicao'   => Carbon::parse($request->data_aquisicao)->format('Y-m-d'),
            'vlc'              => $request->vlc,
            'nova_depreciacao_anual' => $request->nova_depreciacao_anual,
            'valor_residual'   => $request->valor_residual,
            'valor_atualizado' => $request->valor_atualizado,
            'data_reavaliacao' => Carbon::parse($request->data_reavaliacao)->format('Y-m-d'),
            'metodo'           => 'Linear',
            'observacoes'      => $request->observacoes,
        ]);

        // Actualiza o bem
        if ($reavaliacao->bem) {
            $reavaliacao->bem->valor_reavaliado = $request->valor_atualizado;
            $reavaliacao->bem->save();
        }

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Actualizou reavaliação',
                "Usuário {$authUser->name} actualizou a reavaliação ID {$id}."
            );
        }

        return redirect()
            ->route('reavaliacoes.index')
            ->with('success', '✅ Reavaliação actualizada com sucesso!');
    }

    public function destroy($id)
    {
        $reavaliacao = Reavaliacao::findOrFail($id);

        $reavaliacao->delete();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Excluiu reavaliação',
                "Usuário {$authUser->name} removeu a reavaliação ID {$id}."
            );
        }

        return redirect()
            ->route('reavaliacoes.index')
            ->with('success', '🗑️ Reavaliação removida com sucesso!');
    }

    public function exportExcel()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Exportou reavaliações Excel',
                "Usuário {$authUser->name} exportou o relatório de reavaliações em Excel."
            );
        }

        return Excel::download(
            new ReavaliacoesExport,
            'relatorio_reavaliacoes.xlsx'
        );
    }

    public function exportPdf()
    {
        $titulo = 'Relatório de Reavaliações — Histórico';
        $logo = public_path('imagens/ENDE.png');
        $data_geracao = now()->format('d/m/Y H:i:s');
        $usuario = auth()->user()->name ?? 'Sistema';

        $reavaliacao_models = Reavaliacao::with(['bem', 'usuario'])->orderByDesc('data_reavaliacao')->get();

        // Formata dados para a view
        $reavaliacoes = $reavaliacao_models->map(function($r) {
            return [
                'nome_bem' => $r->bem->Nome ?? '-',
                'etiqueta' => $r->bem->Etiqueta ?? '-',
                'data' => optional($r->data_reavaliacao)->format('d/m/Y') ?? '-',
                'valor_anterior' => CurrencyHelper::formatKz($r->valor_inicial ?? 0),
                'valor_novo' => CurrencyHelper::formatKz($r->valor_atualizado ?? 0),
                'estado' => $this->getEstadoReavaliacao($r->valor_inicial, $r->valor_atualizado),
                'usuario' => $r->usuario->name ?? '-',
            ];
        });

        $resumo = [
            'total' => $reavaliacao_models->count(),
            'valor_total_anterior' => CurrencyHelper::formatKz($reavaliacao_models->sum('valor_inicial') ?? 0),
            'valor_total_novo' => CurrencyHelper::formatKz($reavaliacao_models->sum('valor_atualizado') ?? 0),
        ];

        $descricao = 'Relatório de reavaliações de ativos, incluindo análise de depreciação, valores anteriores e atualizados.';

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Exportou reavaliações PDF',
                "Usuário {$authUser->name} exportou o relatório de reavaliações em PDF."
            );
        }

        $pdf = Pdf::loadView('pdf.reavaliacoes', compact('reavaliacoes', 'resumo', 'descricao', 'titulo', 'logo', 'data_geracao', 'usuario'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'marginTop' => 60,
                'marginBottom' => 30,
                'marginLeft' => 15,
                'marginRight' => 15,
            ]);

        return $pdf->download('relatorio_reavaliacoes_' . now()->format('Ymd_His') . '.pdf');
    }

    // Helper para determinar estado da reavaliação
    private function getEstadoReavaliacao($valor_anterior, $valor_novo)
    {
        $anterior = $valor_anterior ?? 0;
        $novo = $valor_novo ?? 0;

        if ($novo > $anterior * 1.1) {
            return 'Ótimo';
        } elseif ($novo > $anterior) {
            return 'Bom';
        } elseif ($novo === $anterior) {
            return 'Regular';
        } else {
            return 'Ruim';
        }
    }
}
