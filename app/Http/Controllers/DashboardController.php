<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bem;
use App\Models\Edificio;
use App\Models\Provincia;
use App\Models\Grupo;
use App\Models\Categoria;
use App\Models\EstadoConservacao;
use App\Models\Manutencao;
use App\Models\Reavaliacao;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Registrar acesso ao dashboard
        $authUser = Auth::user();
        if ($authUser) {
            LogHelper::registrar(
                'Acessou o dashboard',
                'Usuário ' . $authUser->name . ' acessou a tela principal do Dashboard.'
            );
        }

        $now = Carbon::now();

        // Filtros para selects
        $edificios = Edificio::orderBy('Nome')->get();
        $provincias = Provincia::orderBy('Nome')->get();
        $grupos = Grupo::orderBy('Nome')->get();

        // Query base com relacionamentos
        $query = Bem::with([
            'categoria',
            'grupo',
            'estadoConservacao',
            'sala.piso.edificio.provincia'
        ]);

        // Aplicar filtros
        if ($request->filled('provincia_id')) {
            $query->whereHas('sala.piso.edificio.provincia', function($q) use ($request) {
                $q->where('ProvinciaId', $request->provincia_id);
            });
        }

        if ($request->filled('edificio_id')) {
            $query->whereHas('sala.piso.edificio', function($q) use ($request) {
                $q->where('EdificioId', $request->edificio_id);
            });
        }

        if ($request->filled('grupo_id')) {
            $query->whereHas('grupo', function($q) use ($request) {
                $q->where('GrupoId', $request->grupo_id);
            });
        }

        $ativos = $query->get();
        $totalAtivos = $ativos->count();

        // Ativos atualmente em manutenção (status Pendente ou Em Andamento)
        $ativosManutencao = Manutencao::whereIn('status', ['Pendente', 'Em Andamento'])
            ->when($request->filled('provincia_id'), function($q) use ($request) {
                $q->whereHas('bem.sala.piso.edificio.provincia', function($qq) use ($request) {
                    $qq->where('ProvinciaId', $request->provincia_id);
                });
            })
            ->when($request->filled('edificio_id'), function($q) use ($request) {
                $q->whereHas('bem.sala.piso.edificio', function($qq) use ($request) {
                    $qq->where('EdificioId', $request->edificio_id);
                });
            })
            ->when($request->filled('grupo_id'), function($q) use ($request) {
                $q->whereHas('bem.grupo', function($qq) use ($request) {
                    $qq->where('GrupoId', $request->grupo_id);
                });
            })
            ->count();

        // Manutenções do mês atual
        $manutencoesMes = Manutencao::whereMonth('data_manutencao', $now->month)
                                    ->whereYear('data_manutencao', $now->year)
                                    ->count();

        // Valor total patrimonial
        $valorTotalPatrimonial = $ativos->sum('preco_aquisicao');

        // --- Categorias por Grupo ---
        $categoriasLabels = $ativos->map(function($a) {
            $grupoNome = $a->grupo ? $a->grupo->Nome : 'Sem Grupo';
            $categoriaNome = $a->categoria ? $a->categoria->Nome : 'Sem Categoria';
            return $grupoNome . ' - ' . $categoriaNome;
        })->unique()->values();

        $categoriasValues = $categoriasLabels->map(function($label) use ($ativos) {
            return $ativos->filter(function($a) use ($label) {
                $grupoNome = $a->grupo ? $a->grupo->Nome : 'Sem Grupo';
                $categoriaNome = $a->categoria ? $a->categoria->Nome : 'Sem Categoria';
                return ($grupoNome . ' - ' . $categoriaNome) == $label;
            })->count();
        });

        // --- Grupos ---
        $gruposLabels = $grupos->pluck('Nome');
        $gruposValues = $gruposLabels->map(function($g) use ($ativos) {
            return $ativos->filter(function($a) use ($g) {
                return $a->grupo && $a->grupo->Nome === $g;
            })->count();
        });
        $gruposPercent = $gruposValues->map(function($v) use ($totalAtivos) {
            return $totalAtivos > 0 ? round($v / $totalAtivos * 100, 1) : 0;
        });

        // --- Estados de Conservação ---
        $estadosLabels = EstadoConservacao::pluck('Nome');
        $estadosValues = $estadosLabels->map(function($e) use ($ativos) {
            return $ativos->filter(function($a) use ($e) {
                return $a->estadoConservacao && $a->estadoConservacao->Nome === $e;
            })->count();
        });

        // --- Evolução Patrimonial ---
        $ativosOrdenados = $ativos->sortBy('created_at');
        $evolucaoLabels = $ativosOrdenados->pluck('created_at')->map(function($d) {
            return $d ? $d->format('d/m/Y') : '';
        });
        $evolucaoValores = $ativosOrdenados->pluck('preco_aquisicao');

        // --- Ativos por Província ---
        $ativosPorProvincia = Provincia::all()->map(function($prov) use ($ativos) {
            $count = $ativos->filter(function($a) use ($prov) {
                return $a->sala && $a->sala->piso && $a->sala->piso->edificio &&
                       $a->sala->piso->edificio->provincia &&
                       $a->sala->piso->edificio->provincia->ProvinciaId == $prov->ProvinciaId;
            })->count();
            return ['nome' => $prov->Nome, 'ativos' => $count];
        })->filter(function($p) {
            return $p['ativos'] > 0;
        })->values();

        return view('dashboard', compact(
            'edificios','provincias','grupos',
            'totalAtivos','ativosManutencao','manutencoesMes','valorTotalPatrimonial',
            'categoriasLabels','categoriasValues','estadosLabels','estadosValues',
            'evolucaoLabels','evolucaoValores',
            'gruposLabels','gruposValues','gruposPercent','ativosPorProvincia'
        ));
    }
}
