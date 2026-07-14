<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    /**
     * Fazer login e obter token de API
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciais inválidas',
            ], 401);
        }

        $user = Auth::user();

        // Revogar tokens anteriores para segurança
        $user->tokens()->delete();

        // Criar novo token com nome descritivo
        $token = $user->createToken(
            'api-token',
            ['*'],
            now()->addDays(30) // Token expira em 30 dias
        )->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Fazer logout e revogar token
     */
    public function logout(Request $request)
    {
        // Revogar o token atual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ], 200);
    }
}
