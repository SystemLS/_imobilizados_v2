<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\LogHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    /**
     * Exibe o formulário de cadastro de usuário.
     */
    public function create()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou criação de usuário', "Usuário {$authUser->name} abriu o formulário de cadastro de usuário.");
        }

        return view('users.create');
    }

    /**
     * Salva o novo usuário e envia email de boas-vindas.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'perfil' => 'required|in:administrador,gestor,tecnico_cadastro,tecnico_contabilidade,tecnico_manutencao,padrao',
        ]);

        // Gera senha aleatória
        $password = Str::random(10);

        // Cria o usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'perfil' => $request->perfil,
            'password' => Hash::make($password),
        ]);

        // Dispara evento de registro para email verification
        event(new Registered($user));

        // Envia email de boas-vindas
        Mail::to($user->email)->send(new WelcomeUserMail($user, $password));

        if ($authUser = Auth::user()) {
            LogHelper::registrar('Criou usuário', "Usuário {$authUser->name} cadastrou o usuário {$user->name} com email {$user->email}.");
        }

        return redirect()->route('config.index')->with('success', '✅ Usuário cadastrado com sucesso!');
    }

    /**
     * Exibe o formulário para alterar a fotografia do usuário.
     */
    public function editPhoto()
    {
        if ($authUser = Auth::user()) {
            LogHelper::registrar('Acessou edição de fotografia', "Usuário {$authUser->name} abriu o formulário para alterar sua fotografia.");
        }

        return view('users.edit-photo');
    }

    /**
     * Atualiza a fotografia do usuário.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'fotografia' => 'required|image|mimes:jpg,png|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('fotografia')) {
            $file = $request->file('fotografia');
            $path = $file->store('fotografias', 'public');

            // Apaga a foto antiga se existir
            if ($user->fotografia) {
                Storage::disk('public')->delete($user->fotografia);
            }

            $user->fotografia = $path;
            $user->save();

            if ($authUser = Auth::user()) {
                LogHelper::registrar('Atualizou fotografia', "Usuário {$authUser->name} atualizou sua fotografia.");
            }
        }

        return redirect()->route('painel.controle')->with('success', '✅ Fotografia atualizada com sucesso!');
    }

    /**
     * Exporta usuários em PDF
     */
    public function exportPdf()
    {
        $titulo = 'Relatório de Usuários — Sistema';
        $logo = public_path('imagens/ENDE.png');
        $data_geracao = now()->format('d/m/Y H:i:s');
        $usuario = auth()->user()->name ?? 'Sistema';

        $usuarios_models = User::orderBy('name')->get();

        // Formata dados para a view
        $usuarios = $usuarios_models->map(function($user) {
            return [
                'nome' => $user->name,
                'email' => $user->email,
                'perfil' => ucfirst(str_replace('_', ' ', $user->perfil)),
                'perfil_classe' => strtolower(explode('_', $user->perfil)[0] ?? 'operador'),
                'status' => $user->email_verified_at ? 'Ativo' : 'Pendente',
                'status_ativo' => (bool)$user->email_verified_at,
                'data_criacao' => $user->created_at->format('d/m/Y H:i'),
            ];
        });

        $resumo = [
            'total' => $usuarios_models->count(),
            'administradores' => $usuarios_models->where('perfil', 'administrador')->count(),
            'gestores' => $usuarios_models->where('perfil', 'gestor')->count(),
            'tecnicos' => $usuarios_models->where('perfil', 'like', 'tecnico%')->count(),
        ];

        $descricao = 'Lista completa de usuários cadastrados no sistema com seus respectivos perfis e status de ativação.';

        if ($user = Auth::user()) {
            LogHelper::registrar('Exportou usuários PDF', "Usuário {$user->name} exportou relatório de usuários em PDF.");
        }

        $pdf = Pdf::loadView('pdf.usuarios', compact('usuarios', 'resumo', 'descricao', 'titulo', 'logo', 'data_geracao', 'usuario'))
            ->setOptions([
                'marginTop' => 60,
                'marginBottom' => 30,
                'marginLeft' => 15,
                'marginRight' => 15,
            ]);

        return $pdf->download('usuarios_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Exporta usuários em Excel
     */
    public function exportExcel()
    {
        if ($user = Auth::user()) {
            LogHelper::registrar('Exportou usuários Excel', "Usuário {$user->name} exportou relatório de usuários em Excel.");
        }

        return Excel::download(new UsersExport(), 'usuarios_' . now()->format('Ymd_His') . '.xlsx');
    }
}
