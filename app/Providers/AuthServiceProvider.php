<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        \App\Models\Bem::class => \App\Policies\BemPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Gate de acesso ao módulo de manutenção
        Gate::define('manutencao-access', function ($user) {
            // Só usuários com esses perfis podem acessar
            return in_array($user->perfil, ['administrador', 'gestor', 'tecnico_manutencao']);
        });
    }
}
