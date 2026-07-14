<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o usuário está logado e se é administrador
        if (!Auth::check() || Auth::user()->perfil !== 'administrador') {
            abort(403, 'Acesso negado');
        }

        return $next($request);
    }
}
