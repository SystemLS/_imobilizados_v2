<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        // Apenas usuários logados
        if (Auth::check()) {
            $user = Auth::user();

            // Se precisa trocar a senha e não está na rota de alteração de senha
            if ($user->force_password_change
                && !$request->is('senha/alterar')
                && !$request->is('logout')) {

                return redirect()->route('senha.alterar');
            }
        }

        return $next($request);
    }
}
