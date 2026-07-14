<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Formata um valor monetário em Kwanzas (Kz) de Angola
     *
     * @param float $value - Valor a formatar
     * @param int $decimals - Número de casas decimais (padrão 2)
     * @param bool $symbol - Incluir símbolo Kz (padrão true)
     * @return string
     *
     * Exemplos:
     * formatKz(1234.56)        → "Kz 1.234,56"
     * formatKz(1000)           → "Kz 1.000,00"
     * formatKz(500.5, 2, false) → "500,50"
     */
    public static function formatKz($value, $decimals = 2, $symbol = true)
    {
        $formatted = number_format((float)$value, $decimals, ',', '.');

        return $symbol ? "Kz {$formatted}" : $formatted;
    }

    /**
     * Formata percentual
     *
     * @param float $value - Valor do percentual
     * @return string
     */
    public static function formatPercent($value)
    {
        return number_format((float)$value, 2, ',', '.') . '%';
    }

    /**
     * Formata um intervalo de valores monetários
     *
     * @param float $start - Valor inicial
     * @param float $end - Valor final
     * @return string
     *
     * Exemplo: formatKzRange(100, 500) → "Kz 100,00 — Kz 500,00"
     */
    public static function formatKzRange($start, $end)
    {
        return self::formatKz($start) . ' — ' . self::formatKz($end);
    }

    /**
     * Extrai apenas o valor numérico de uma string formatada
     *
     * @param string $value - String formatada
     * @return float
     */
    public static function extractNumeric($value)
    {
        $value = str_replace('Kz ', '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return (float)$value;
    }
}
