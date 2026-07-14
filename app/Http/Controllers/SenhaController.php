<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use App\Mail\SenhaAlteradaMail;

class SenhaController extends Controller
{
    /**
     * Exibe o formulário de alteração de senha
     */
    public function edit()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou formulário de alteração de senha',
                "Usuário {$authUser->name} acessou o formulário para alterar a senha."
            );
        }

        return view('auth.passwords.change'); // View do formulário
    }

    /**
     * Atualiza a senha do usuário logado
     */
    public function update(Request $request)
    {
        // Validação
        $request->validate([
            'password' => 'required|string|confirmed|min:6',
        ]);

        // Usuário logado
        $user = Auth::user();

        // Atualiza a senha no banco
        $user->password = Hash::make($request->password);
        $user->save();

        // Log da alteração de senha
        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Alterou senha',
                "Usuário {$authUser->name} alterou a própria senha."
            );
        }

        try {
            // Envia email imediatamente
            Mail::to($user->email)->queue(new SenhaAlteradaMail($user));
        } catch (\Exception $e) {
            // Log caso haja falha no envio do email
            if ($authUser = Auth::user()) {
                LogHelper::registrar(
                    'Falha ao enviar email de alteração de senha',
                    "Erro ao enviar email para {$user->email}: {$e->getMessage()}"
                );
            }
        }

        return redirect()->route('senha.alterar')
            ->with('success', '✅ Senha alterada com sucesso! Um email de confirmação foi enviado.');
    }
}
