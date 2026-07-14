<?php

namespace App\Helpers;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    /**
     * Registra um log no sistema
     *
     * @param string $evento
     * @param string $descricao
     * @return void
     */
    public static function registrar(string $evento, string $descricao)
    {
        $usuarioId = Auth::check() ? Auth::id() : null;

        Log::create([
            'usuario_id' => $usuarioId,
            'evento'     => $evento,
            'descricao'  => $descricao,
        ]);
    }
}
