<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckPerfil;
use App\Http\Middleware\CheckManutencaoAccess;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            // Registrar manualmente o middleware de perfil
    Route::aliasMiddleware('perfil', CheckPerfil::class);
    Route::aliasMiddleware('manutencao-access', CheckManutencaoAccess::class);
    }


}
