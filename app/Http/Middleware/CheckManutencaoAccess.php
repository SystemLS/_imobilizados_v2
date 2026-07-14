<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CheckManutencaoAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (Gate::denies('manutencao-access')) {
            abort(403, 'Acesso negado: você não tem permissão para acessar este módulo.');
        }

        return $next($request);
    }
}
