<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Helpers\LogHelper;

class ProfileController extends Controller
{
    /**
     * Renderiza o Painel de Controlo
     */
    public function painelControle()
    {
        $user = Auth::user(); // Usuário logado

        if ($user) {
            LogHelper::registrar('Acessou Painel de Controlo', "Usuário {$user->name} acessou o Painel de Controlo.");
        }

        return view('profile.painel-controle', compact('user'));
    }

    /**
     * Exibe o formulário de edição do perfil
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        if ($user) {
            LogHelper::registrar('Acessou edição de perfil', "Usuário {$user->name} abriu o formulário de edição de perfil.");
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Atualiza as informações do perfil do usuário
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user) {
            LogHelper::registrar('Atualizou perfil', "Usuário {$user->name} atualizou seu perfil.");
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Exclui a conta do usuário
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user) {
            LogHelper::registrar('Excluiu conta', "Usuário {$user->name} excluiu sua conta.");
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
