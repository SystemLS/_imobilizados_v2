<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Hash;

class ConfigController extends Controller
{
    // Lista todos os usuários
    public function index()
    {
        $usuarios = User::all();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou a lista de usuários',
                'Usuário ' . $authUser->name . ' visualizou a lista de usuários cadastrados.'
            );
        }

        return view('config.index', compact('usuarios'));
    }

    // Altera o perfil do usuário existente
    public function updatePerfil(Request $request, User $user)
    {
        $request->validate([
            'perfil' => 'required|in:administrador,gestor,tecnico_contabilidade,tecnico_manutencao,tecnico_cadastro,padrao',
        ]);

        $oldPerfil = $user->perfil;
        $user->perfil = $request->perfil;
        $user->save();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Alterou perfil de usuário',
                'Usuário ' . $authUser->name . ' alterou o perfil de ' . $user->name . ' de "' . $oldPerfil . '" para "' . $user->perfil . '".'
            );
        }

        return redirect()->route('config.index')->with('success', 'Perfil atualizado com sucesso!');
    }

    // Formulário de edição de usuário
    public function edit(User $user)
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Visualizou edição de usuário',
                'Usuário ' . $authUser->name . ' abriu o formulário de edição de ' . $user->name . '.'
            );
        }

        return view('config.edit', compact('user'));
    }

    // Página de integração Power BI
    public function integracao()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Acessou a integração Power BI',
                'Usuário ' . $authUser->name . ' visualizou a página de integração Power BI.'
            );
        }

        return view('config.integracao');
    }

    // Atualiza nome, email e perfil do usuário
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'perfil' => 'required|in:administrador,gestor,tecnico_contabilidade,tecnico_manutencao,tecnico_cadastro,padrao',
        ]);

        $oldData = $user->only(['name', 'email', 'perfil']);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'perfil' => $request->perfil,
        ]);

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Atualizou usuário',
                'Usuário ' . $authUser->name . ' atualizou ' . $user->name . ' (Antes: ' . json_encode($oldData) . ', Depois: ' . json_encode($user->only(['name', 'email', 'perfil'])) . ')'
            );
        }

        return redirect()->route('config.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    // Deleta um usuário
    public function destroy(User $user)
    {
        $userName = $user->name;
        $user->delete();

        if ($authUser = Auth::user()) {
            LogHelper::registrar(
                'Deletou usuário',
                'Usuário ' . $authUser->name . ' removeu o usuário ' . $userName . '.'
            );
        }

        return redirect()->route('config.index')->with('success', 'Usuário removido com sucesso!');
    }
}
