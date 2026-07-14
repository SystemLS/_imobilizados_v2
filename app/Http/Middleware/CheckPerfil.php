<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPerfil
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $perfils)
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    // Normaliza a string: permite vírgula, || ou espaços como separador
    $allowedProfiles = preg_split('/\s*\|\|\s*|,\s*|\s+/', $perfils);
    $allowedProfiles = array_map(fn($p) => strtolower(trim($p)), $allowedProfiles);

    $userPerfil = strtolower(trim($user->perfil));

    // Checagem direta
    if (!in_array($userPerfil, $allowedProfiles)) {
        abort(403, 'Acesso negado — perfil não autorizado.');
    }

    return $next($request);
}

}
