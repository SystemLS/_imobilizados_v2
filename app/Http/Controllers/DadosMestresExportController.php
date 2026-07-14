<?php

namespace App\Http\Controllers;

use App\Exports\DadosMestresExport;
use App\Exports\PdfExporter;
use App\Helpers\LogHelper;
use App\Models\Categoria;
use App\Models\CondicaoAmbiental;
use App\Models\Edificio;
use App\Models\EstadoConservacao;
use App\Models\Grupo;
use App\Models\Material;
use App\Models\Piso;
use App\Models\Provincia;
use App\Models\Sala;
use App\Models\Subcategoria;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DadosMestresExportController extends Controller
{
    public function exportExcel(Request $request, string $section)
    {
        $exportData = $this->buildSectionData($section, $request);
        $usuarioLog = Auth::user()->name ?? 'Sistema';

        if (empty($exportData['rows'])) {
            LogHelper::registrar(
                "Exportou {$exportData['title']} Excel",
                "Usuário {$usuarioLog} solicitou exportação Excel de {$section} sem registros."
            );
        } else {
            LogHelper::registrar(
                "Exportou {$exportData['title']} Excel",
                "Usuário {$usuarioLog} exportou {$section} em Excel."
            );
        }

        return Excel::download(
            new DadosMestresExport($exportData['headers'], $exportData['rows'], $exportData['title']),
            $exportData['file_name'] . '_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportPdf(Request $request, string $section)
    {
        $exportData = $this->buildSectionData($section, $request);
        $usuarioLog = Auth::user()->name ?? 'Sistema';

        if (empty($exportData['rows'])) {
            LogHelper::registrar(
                "Exportou {$exportData['title']} PDF",
                "Usuário {$usuarioLog} solicitou exportação PDF de {$section} sem registros."
            );
        } else {
            LogHelper::registrar(
                "Exportou {$exportData['title']} PDF",
                "Usuário {$usuarioLog} exportou {$section} em PDF."
            );
        }

        $pdf = PdfExporter::gerar(
            $exportData['title'],
            $exportData['description'],
            'pdf.dados_mestres',
            [
                'headers' => $exportData['headers'],
                'rows' => $exportData['rows'],
                'usuario' => $usuarioLog,
            ]
        );

        return PdfExporter::download(
            $pdf,
            $exportData['file_name'] . '_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    protected function buildSectionData(string $section, Request $request): array
    {
        $section = Str::of($section)->lower()->trim()->__toString();
        $search = $request->input('q');

        return match ($section) {
            'grupos' => $this->buildGruposData($search),
            'categorias' => $this->buildCategoriasData($request, $search),
            'subcategorias' => $this->buildSubcategoriasData($search),
            'provincias' => $this->buildProvinciasData($search),
            'edificios' => $this->buildEdificiosData($request, $search),
            'pisos' => $this->buildPisosData($request, $search),
            'salas' => $this->buildSalasData($request, $search),
            'materiais' => $this->buildMateriaisData($search),
            'estado_conservacao' => $this->buildEstadoConservacaoData($search),
            'condicoes_ambientais' => $this->buildCondicoesAmbientaisData($search),
            default => throw new \InvalidArgumentException("Seção de exportação inválida: {$section}"),
        };
    }

    protected function buildGruposData(?string $search): array
    {
        $query = Grupo::query();

        if (!empty($search)) {
            $query->where('Nome', 'LIKE', "%{$search}%");
        }

        $grupos = $query->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Grupos',
            'description' => 'Lista de grupos cadastrados no sistema de Gestão de Ativos Imobilizado.',
            'headers' => ['Nome'],
            'rows' => $grupos->map(fn($item) => [$item->Nome])->toArray(),
            'file_name' => 'dados_mestres_grupos',
        ];
    }

    protected function buildCategoriasData(Request $request, ?string $search): array
    {
        $query = Categoria::with('grupo');

        if ($request->filled('GrupoId')) {
            $query->where('GrupoId', $request->input('GrupoId'));
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('Nome', 'LIKE', "%{$search}%")
                  ->orWhereHas('grupo', fn($g) => $g->where('Nome', 'LIKE', "%{$search}%"));
            });
        }

        $categorias = $query->orderBy('GrupoId')->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Categorias',
            'description' => 'Lista de categorias e seus grupos associados no sistema.',
            'headers' => ['Grupo', 'Categoria'],
            'rows' => $categorias->map(fn($item) => [
                $item->grupo->Nome ?? '-',
                $item->Nome,
            ])->toArray(),
            'file_name' => 'dados_mestres_categorias',
        ];
    }

    protected function buildSubcategoriasData(?string $search): array
    {
        $query = Subcategoria::with('categoria.grupo');

        if (!empty($search)) {
            $query->where('Nome', 'LIKE', "%{$search}%");
        }

        $subcategorias = $query->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Subcategorias',
            'description' => 'Lista de subcategorias com suas categorias e grupos.',
            'headers' => ['Grupo', 'Categoria', 'Subcategoria'],
            'rows' => $subcategorias->map(fn($item) => [
                $item->categoria->grupo->Nome ?? '-',
                $item->categoria->Nome ?? '-',
                $item->Nome,
            ])->toArray(),
            'file_name' => 'dados_mestres_subcategorias',
        ];
    }

    protected function buildProvinciasData(?string $search): array
    {
        $query = Provincia::query();

        if (!empty($search)) {
            $query->where('Nome', 'LIKE', "%{$search}%");
        }

        $provincias = $query->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Províncias',
            'description' => 'Lista de províncias cadastradas no sistema.',
            'headers' => ['Nome'],
            'rows' => $provincias->map(fn($item) => [$item->Nome])->toArray(),
            'file_name' => 'dados_mestres_provincias',
        ];
    }

    protected function buildEdificiosData(Request $request, ?string $search): array
    {
        $query = Edificio::with('provincia');

        if ($request->filled('ProvinciaId')) {
            $query->where('ProvinciaId', $request->input('ProvinciaId'));
        }

        if (!empty($search)) {
            $query->where('Nome', 'LIKE', "%{$search}%");
        }

        $edificios = $query->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Edifícios',
            'description' => 'Lista de edifícios e suas províncias associadas.',
            'headers' => ['Província', 'Edifício'],
            'rows' => $edificios->map(fn($item) => [
                $item->provincia->Nome ?? '-',
                $item->Nome,
            ])->toArray(),
            'file_name' => 'dados_mestres_edificios',
        ];
    }

    protected function buildPisosData(Request $request, ?string $search): array
    {
        $query = Piso::with('edificio');

        if ($request->filled('EdificioId')) {
            $query->where('EdificioId', $request->input('EdificioId'));
        }

        if (!empty($search)) {
            $query->where('Nome', 'LIKE', "%{$search}%");
        }

        $pisos = $query->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Pisos',
            'description' => 'Lista de pisos e seus edifícios correspondentes.',
            'headers' => ['Edifício', 'Piso'],
            'rows' => $pisos->map(fn($item) => [
                $item->edificio->Nome ?? '-',
                $item->Nome,
            ])->toArray(),
            'file_name' => 'dados_mestres_pisos',
        ];
    }

    protected function buildSalasData(Request $request, ?string $search): array
    {
        $query = Sala::with('piso.edificio.provincia');

        if ($request->filled('PisoId')) {
            $query->where('PisoId', $request->input('PisoId'));
        }

        if (!empty($search)) {
            $query->where('Nome', 'LIKE', "%{$search}%");
        }

        $salas = $query->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Salas',
            'description' => 'Lista de salas com seus pisos, edifícios e províncias.',
            'headers' => ['Província', 'Edifício', 'Piso', 'Sala'],
            'rows' => $salas->map(fn($item) => [
                $item->piso->edificio->provincia->Nome ?? '-',
                $item->piso->edificio->Nome ?? '-',
                $item->piso->Nome ?? '-',
                $item->Nome,
            ])->toArray(),
            'file_name' => 'dados_mestres_salas',
        ];
    }

    protected function buildMateriaisData(?string $search): array
    {
        $query = Material::query();

        if (!empty($search)) {
            $query->where('Nome', 'LIKE', "%{$search}%");
        }

        $materiais = $query->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Materiais',
            'description' => 'Lista de materiais usados no sistema de Ativos.',
            'headers' => ['Nome'],
            'rows' => $materiais->map(fn($item) => [$item->Nome])->toArray(),
            'file_name' => 'dados_mestres_materiais',
        ];
    }

    protected function buildEstadoConservacaoData(?string $search): array
    {
        $query = EstadoConservacao::query();

        if (!empty($search)) {
            $query->where('Nome', 'LIKE', "%{$search}%");
        }

        $estados = $query->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Estado de Conservação',
            'description' => 'Lista de estados de conservação cadastrados no sistema.',
            'headers' => ['Nome', 'Descrição'],
            'rows' => $estados->map(fn($item) => [$item->Nome, $item->Descricao ?? '-'])->toArray(),
            'file_name' => 'dados_mestres_estado_conservacao',
        ];
    }

    protected function buildCondicoesAmbientaisData(?string $search): array
    {
        $query = CondicaoAmbiental::query();

        if (!empty($search)) {
            $query->where('Nome', 'LIKE', "%{$search}%");
        }

        $condicoes = $query->orderBy('Nome')->get();

        return [
            'title' => 'Relatório de Condições Ambientais',
            'description' => 'Lista de condições ambientais cadastradas no sistema.',
            'headers' => ['Nome', 'Descrição'],
            'rows' => $condicoes->map(fn($item) => [$item->Nome, $item->Descricao ?? '-'])->toArray(),
            'file_name' => 'dados_mestres_condicoes_ambientais',
        ];
    }
}
