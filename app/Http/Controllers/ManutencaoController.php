<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manutencao;
use App\Models\Bem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Carbon\Carbon;
use App\Exports\ManutencoesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;



class ManutencaoController extends Controller
{
    /**
     * Lista manutenções
     */
    public function index(Request $request)
    {
        $query = Manutencao::with('bem');

        if ($request->filled('bem')) {
            $query->whereHas('bem', fn($q) =>
                $q->where('Nome', 'like', '%' . $request->bem . '%')
            );
        }

        if ($request->filled('etiqueta')) {
            $query->whereHas('bem', fn($q) =>
                $q->where('Etiqueta', 'like', '%' . $request->etiqueta . '%')
            );
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('responsavel')) {
            $query->where('responsavel', 'like', '%' . $request->responsavel . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $manutencoes = $query->orderByDesc('data_manutencao')->paginate(10);

        if ($user = Auth::user()) {
            LogHelper::registrar(
                'Acessou lista de manutenções',
                "Usuário {$user->name} visualizou a lista de manutenções."
            );
        }

        return view('manutencoes.index', compact('manutencoes'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $bens = Bem::orderBy('Nome')->get();
        $tecnicos = User::where('perfil', 'tecnico_manutencao')->orderBy('name')->get();

        if ($user = Auth::user()) {
            LogHelper::registrar(
                'Acessou criação de manutenção',
                "Usuário {$user->name} abriu o formulário de criação de manutenção."
            );
        }

        return view('manutencoes.create', compact('bens', 'tecnicos'));
    }

    /**
     * Guardar manutenção
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bem_id' => 'required|exists:bens,BemId',
            'tipo' => 'required|in:Preventiva,Corretiva',
            'descricao' => 'required|string|min:10',
            'data_manutencao' => 'required|date',
            'DataConclusao' => 'nullable|date|after:data_manutencao',
            'status' => 'required|in:Pendente,Em Andamento,Concluída,Cancelada',
            'responsavel' => 'required|string',
        ], [
            'bem_id.required' => 'O bem é obrigatório.',
            'bem_id.exists' => 'O bem selecionado não é válido.',

            'tipo.required' => 'O tipo de manutenção é obrigatório.',
            'tipo.in' => 'Tipo de manutenção inválido.',

            'descricao.required' => 'A descrição da manutenção é obrigatória.',
            'descricao.min' => 'A descrição deve conter no mínimo 10 caracteres.',

            'data_manutencao.required' => 'A data da manutenção é obrigatória.',
            'data_manutencao.date' => 'A data da manutenção é inválida.',

            'DataConclusao.date' => 'A data de conclusão é inválida.',
            'DataConclusao.after' => 'A data de conclusão deve ser posterior à data da manutenção.',

            'status.required' => 'O estado da manutenção é obrigatório.',
            'status.in' => 'Estado da manutenção inválido.',

            'responsavel.required' => 'O responsável pela manutenção é obrigatório.',
        ]);

        if ($validated['status'] === 'Concluída' && empty($validated['DataConclusao'])) {
            return back()
                ->withErrors(['DataConclusao' => 'Informe a data de conclusão para manutenções concluídas.'])
                ->withInput();
        }

        $validated['data_manutencao'] = Carbon::parse($validated['data_manutencao']);
        $validated['DataConclusao'] = $validated['DataConclusao']
            ? Carbon::parse($validated['DataConclusao'])
            : null;

        $manutencao = Manutencao::create($validated);

        $bem = Bem::find($validated['bem_id']);
        if ($bem) {
            $bem->manutencao = "Última manutenção em {$manutencao->data_manutencao->format('d/m/Y')} ({$manutencao->status})";
            $bem->save();
        }

        if ($user = Auth::user()) {
            LogHelper::registrar(
                'Criou manutenção',
                "Usuário {$user->name} criou a manutenção ID {$manutencao->id} para o bem {$bem->Nome}."
            );
        }

        return redirect()->route('manutencoes.index')
            ->with('success', 'Manutenção registada com sucesso.');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $manutencao = Manutencao::with('bem')->findOrFail($id);
        $bens = Bem::orderBy('Nome')->get();
        $tecnicos = User::where('perfil', 'tecnico_manutencao')->orderBy('name')->get();

        if ($user = Auth::user()) {
            LogHelper::registrar(
                'Acessou edição de manutenção',
                "Usuário {$user->name} abriu a manutenção ID {$id} para edição."
            );
        }

        return view('manutencoes.edit', compact('manutencao', 'bens', 'tecnicos'));
    }

    /**
     * Actualizar manutenção
     */
    public function update(Request $request, $id)
    {
        $manutencao = Manutencao::findOrFail($id);

        $validated = $request->validate([
            'bem_id' => 'required|exists:bens,BemId',
            'tipo' => 'required|in:Preventiva,Corretiva',
            'descricao' => 'required|string|min:10',
            'data_manutencao' => 'required|date',
            'DataConclusao' => 'nullable|date|after:data_manutencao',
            'status' => 'required|in:Pendente,Em Andamento,Concluída,Cancelada',
            'responsavel' => 'required|string',
        ], [
            'DataConclusao.after' => 'A data de conclusão deve ser posterior à data da manutenção.',
        ]);

        if ($validated['status'] === 'Concluída' && empty($validated['DataConclusao'])) {
            return back()
                ->withErrors(['DataConclusao' => 'A data de conclusão é obrigatória quando o estado é "Concluída".'])
                ->withInput();
        }

        // 🔒 Não permitir alteração do bem
        $validated['bem_id'] = $manutencao->bem_id;

        $validated['data_manutencao'] = Carbon::parse($validated['data_manutencao']);
        $validated['DataConclusao'] = $validated['DataConclusao']
            ? Carbon::parse($validated['DataConclusao'])
            : null;

        $manutencao->update($validated);

        $bem = $manutencao->bem;
        if ($bem) {
            $bem->manutencao = "Última manutenção em {$manutencao->data_manutencao->format('d/m/Y')} ({$manutencao->status})";
            $bem->save();
        }

        if ($user = Auth::user()) {
            LogHelper::registrar(
                'Actualizou manutenção',
                "Usuário {$user->name} actualizou a manutenção ID {$id} do bem {$bem->Nome}."
            );
        }

        return redirect()->route('manutencoes.index')
            ->with('success', 'Manutenção actualizada com sucesso.');
    }

    /**
     * Remover manutenção
     */
    public function destroy($id)
    {
        $manutencao = Manutencao::findOrFail($id);
        $bem = $manutencao->bem;
        $manutencao->delete();

        if ($user = Auth::user()) {
            LogHelper::registrar(
                'Removeu manutenção',
                "Usuário {$user->name} removeu a manutenção ID {$id} do bem {$bem->Nome}."
            );
        }

        return redirect()->route('manutencoes.index')
            ->with('success', 'Manutenção removida com sucesso.');
    }


            /**
         * Exportar manutenções para Excel
         */
        public function exportExcel(Request $request)
        {
            $filtros = $request->only([
                'bem',
                'etiqueta',
                'tipo',
                'responsavel',
                'status'
            ]);

            if ($user = Auth::user()) {
                LogHelper::registrar(
                    'Exportou manutenções (Excel)',
                    "Usuário {$user->name} exportou a lista de manutenções em Excel."
                );
            }

            return Excel::download(
                new ManutencoesExport($filtros),
                'manutencoes_' . now()->format('Ymd_His') . '.xlsx'
            );
        }

        public function exportPdf(Request $request)
        {
            $titulo = 'Relatório de Manutenções — Histórico';
            $logo = public_path('imagens/ENDE.png');
            $data_geracao = now()->format('d/m/Y H:i:s');
            $usuario = auth()->user()->name ?? 'Sistema';

            $query = Manutencao::with('bem');

            if ($request->bem) {
                $query->whereHas('bem', function ($q) use ($request) {
                    $q->where('Nome', 'like', '%' . $request->bem . '%');
                });
            }

            if ($request->etiqueta) {
                $query->whereHas('bem', function ($q) use ($request) {
                    $q->where('Etiqueta', 'like', '%' . $request->etiqueta . '%');
                });
            }

            if ($request->tipo) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->responsavel) {
                $query->where('responsavel', 'like', '%' . $request->responsavel . '%');
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $manutencoes = $query->orderByDesc('data_manutencao')->get();

            // Formata dados para a view
            $manutencoes = $manutencoes->map(function($m) {
                return [
                    'Nome' => $m->bem->Nome ?? '-',
                    'Etiqueta' => $m->bem->Etiqueta ?? '-',
                    'TipoManutencao' => $m->tipo ?? '-',
                    'DataInicio' => optional($m->data_manutencao)->format('d/m/Y') ?? '-',
                    'DataConclusao' => optional($m->DataConclusao)->format('d/m/Y') ?? '-',
                    'Status' => ucfirst($m->status) ?? 'Pendente',
                    'Responsavel' => $m->responsavel ?? '-',
                ];
            });

            $resumo = [
                'total' => $manutencoes->count(),
                'concluidas' => $manutencoes->where('Status', 'Concluída')->count(),
                'pendentes' => $manutencoes->where('Status', 'Pendente')->count(),
                'em_progresso' => $manutencoes->where('Status', 'Em Progresso')->count(),
            ];

            $descricao = 'Histórico completo de manutenções realizadas nos ativos, incluindo dados de início, conclusão, tipo e responsável.';

            if ($user = Auth::user()) {
                LogHelper::registrar(
                    'Exportou manutenções (PDF)',
                    "Usuário {$user->name} exportou a lista de manutenções em PDF."
                );
            }

            $pdf = Pdf::loadView('pdf.manutencoes', compact('manutencoes', 'resumo', 'descricao', 'titulo', 'logo', 'data_geracao', 'usuario'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'marginTop' => 60,
                    'marginBottom' => 30,
                    'marginLeft' => 15,
                    'marginRight' => 15,
                ]);

            return $pdf->download(
                'manutencoes_' . now()->format('Ymd_His') . '.pdf'
            );
        }


}
