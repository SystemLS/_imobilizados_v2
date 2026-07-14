<?php

namespace App\Exports;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfExporter
{
    /**
     * Configurações padrão do PDF
     */
    protected static array $defaultOptions = [
        'marginTop' => 60,
        'marginBottom' => 30,
        'marginLeft' => 15,
        'marginRight' => 15,
    ];

    /**
     * Cores do sistema (Tailwind palette)
     */
    public static array $colores = [
        'primary' => '#1e3a8a',        // blue-900
        'secondary' => '#dc2626',      // red-600
        'accent' => '#0369a1',         // sky-600
        'success' => '#16a34a',        // green-600
        'warning' => '#ea580c',        // orange-600
        'danger' => '#dc2626',         // red-600
        'light' => '#f8fafc',          // slate-50
        'muted' => '#94a3b8',          // slate-400
        'dark' => '#1e293b',           // slate-800
        'border' => '#e2e8f0',         // slate-200
        'hover' => '#eef2ff',          // blue-50
    ];

    /**
     * Gera um PDF padrão com logo, titulo, descrição e tabela
     *
     * @param string $titulo - Título do relatório
     * @param string $descricao - Descrição/subtítulo do módulo
     * @param string $view - View blade com o conteúdo (recebe $data)
     * @param array $data - Dados a passar para a view
     * @param string $nome_arquivo - Nome do arquivo PDF gerado
     * @param array $opcoes - Opções adicionais do dompdf
     *
     * @return \Barryvdh\DomPDF\PDF
     */
    public static function gerar(
        string $titulo,
        string $descricao,
        string $view,
        array $data = [],
        string $nome_arquivo = 'relatorio.pdf',
        array $opcoes = []
    ) {
        $opcoes = array_merge(self::$defaultOptions, $opcoes);

        // Prepara dados para a view
        $pdfData = array_merge($data, [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'data_geracao' => now()->format('d/m/Y H:i:s'),
            'logo' => public_path('imagens/ENDE.png'),
            'cores' => self::$colores,
        ]);

        return Pdf::loadView($view, $pdfData)
                  ->setOptions($opcoes)
                  ->setOption('isPhpEnabled', true);
    }

    /**
     * Retorna o PDF para download
     */
    public static function download($pdf, string $nome_arquivo = 'relatorio.pdf')
    {
        return $pdf->download($nome_arquivo);
    }

    /**
     * Retorna o PDF para visualização inline
     */
    public static function stream($pdf)
    {
        return $pdf->stream();
    }

    /**
     * Gera HTML para header padrão
     */
    public static function headerHtml(string $titulo, string $logo_path): string
    {
        $cores = self::$colores;
        return <<<HTML
        <div style="text-align: center; border-bottom: 3px solid {$cores['primary']}; padding-bottom: 10px; margin-bottom: 15px;">
            <img src="{$logo_path}" style="height: 50px; margin-bottom: 10px;">
            <h1 style="margin: 5px 0; color: {$cores['primary']}; font-size: 20px; font-weight: bold;">
                {$titulo}
            </h1>
        </div>
        HTML;
    }

    /**
     * Gera HTML para descrição do módulo
     */
    public static function descricaoHtml(string $descricao, string $icone = '📋'): string
    {
        $cores = self::$colores;
        return <<<HTML
        <div style="background-color: {$cores['light']}; border-left: 4px solid {$cores['secondary']}; padding: 10px; margin-bottom: 15px;">
            <p style="margin: 0; color: {$cores['dark']}; font-size: 11px; line-height: 1.5;">
                <strong>{$icone} {$descricao}</strong>
            </p>
        </div>
        HTML;
    }

    /**
     * Estilo CSS global para PDFs
     */
    public static function cssGlobal(): string
    {
        $cores = self::$colores;
        return <<<CSS
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'DejaVu Sans', sans-serif;
                font-size: 10px;
                color: {$cores['dark']};
                line-height: 1.4;
            }

            header {
                position: fixed;
                top: -50px;
                left: 0;
                right: 0;
                height: 50px;
                overflow: hidden;
            }

            footer {
                position: fixed;
                bottom: -30px;
                left: 0;
                right: 0;
                height: 30px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 9px;
                color: {$cores['muted']};
                border-top: 1px solid {$cores['border']};
                padding: 5px 15px;
            }

            main {
                margin-top: 20px;
            }

            /* Tabelas */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            thead {
                background-color: {$cores['primary']};
                color: white;
            }

            th {
                padding: 8px;
                text-align: left;
                font-weight: bold;
                border: 1px solid {$cores['border']};
                font-size: 10px;
            }

            td {
                padding: 6px 8px;
                border: 1px solid {$cores['border']};
                font-size: 9px;
            }

            tbody tr:nth-child(odd) {
                background-color: {$cores['light']};
            }

            tbody tr:nth-child(even) {
                background-color: white;
            }

            /* Badges/Status */
            .badge {
                display: inline-block;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 8px;
                font-weight: bold;
            }

            .badge-primary {
                background-color: {$cores['primary']};
                color: white;
            }

            .badge-success {
                background-color: {$cores['success']};
                color: white;
            }

            .badge-warning {
                background-color: {$cores['warning']};
                color: white;
            }

            .badge-danger {
                background-color: {$cores['danger']};
                color: white;
            }

            .badge-secondary {
                background-color: {$cores['muted']};
                color: white;
            }

            /* Caixas de informação */
            .info-box {
                background-color: {$cores['light']};
                border-left: 4px solid {$cores['accent']};
                padding: 8px;
                margin-bottom: 15px;
            }

            .info-box strong {
                color: {$cores['primary']};
            }

            /* Rodapé customizado */
            .rodape-info {
                margin-top: 20px;
                padding-top: 10px;
                border-top: 1px solid {$cores['border']};
                font-size: 8px;
                color: {$cores['muted']};
            }

            /* Page-break */
            .page-break {
                page-break-after: always;
            }

            /* Títulos */
            h1 {
                font-size: 16px;
                color: {$cores['primary']};
                margin-bottom: 10px;
            }

            h2 {
                font-size: 13px;
                color: {$cores['primary']};
                margin-top: 15px;
                margin-bottom: 8px;
                border-bottom: 2px solid {$cores['secondary']};
                padding-bottom: 3px;
            }

            h3 {
                font-size: 11px;
                color: {$cores['accent']};
                margin-top: 10px;
                margin-bottom: 5px;
            }

            /* Parágrafos */
            p {
                margin-bottom: 8px;
            }

            strong {
                color: {$cores['primary']};
            }

            em {
                color: {$cores['muted']};
            }
        </style>
        CSS;
    }
}
