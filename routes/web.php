<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    ProfileController,
    DashboardController,
    BemController,
    ConfigController,
    UserController,
    ApiController,
    SalaController,
    ReavaliacaoController,
    ManutencaoController,
    DadosMestresController,
    DadosMestresExportController,
    ProvinciaController,
    EdificioController,
    SubcategoriaController,
    SenhaController,
    LogController,
    FollowUpController,
    InventarioController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Arquivo de rotas do sistema de Gestão de Ativos
|--------------------------------------------------------------------------
*/

// ======================== RAIZ DO SISTEMA ========================
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ======================== ROTAS PARA VERIFICAÇÃO DE EMAIL ========================


Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // marca o email como verificado
    return redirect('/dashboard')->with('verified', 'E-mail verificado com sucesso!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'E-mail de verificação reenviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// ======================== ROTAS AUTENTICADAS ========================
Route::middleware(['auth', 'verified'])->group(function () {

    // ---------------- DASHBOARD ----------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---------------- PERFIL DO USUÁRIO ----------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/painel-controle', [ProfileController::class, 'painelControle'])->name('painel.controle');

    // ---------------- GESTÃO DE ATIVOS ----------------
    Route::middleware(['perfil:administrador||gestor||tecnico_cadastro||tecnico_contabilidade'])->group(function () {
        Route::get('/ativos/create', [BemController::class, 'create'])->name('ativos.create');
        Route::post('/ativos', [BemController::class, 'store'])->name('ativos.store');
        Route::get('/ativos/{bem}/edit', [BemController::class, 'edit'])->name('ativos.edit');
        Route::put('/ativos/{bem}', [BemController::class, 'update'])->name('ativos.update');
        Route::delete('/ativos/{bem}', [BemController::class, 'destroy'])->name('ativos.destroy');
    });

    Route::prefix('ativos')->group(function () {
        Route::get('/', [BemController::class, 'index'])->name('ativos.index');
        Route::get('/dados-mestres', [DadosMestresController::class, 'index'])->name('dados_mestres.index');

        // AJAX
        Route::get('/subcategorias-por-categoria/{categoriaId}', [BemController::class, 'getSubcategorias'])->name('subcategorias.por.categoria');

        // Página de detalhes
        Route::get('/{bem}', [BemController::class, 'show'])->name('ativos.show');
    });

    // ---------------- CONFIGURAÇÕES (ADMINISTRADOR) ----------------
    Route::prefix('config')->name('config.')->group(function () {
        Route::get('/', [ConfigController::class, 'index'])->name('index');
        Route::get('/integracao', [ConfigController::class, 'integracao'])->name('integracao');
        Route::patch('/users/{user}/perfil', [ConfigController::class, 'updatePerfil'])->name('users.updatePerfil');
        Route::get('/users/{user}/edit', [ConfigController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [ConfigController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [ConfigController::class, 'destroy'])->name('users.destroy');
    });


    Route::middleware(['auth', 'perfil:administrador'])->group(function () {
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('/logs/export', [LogController::class, 'export'])->name('logs.export');
    });

    // ---------------- GESTÃO DE USUÁRIOS ----------------
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/perfil/fotografia', [UserController::class, 'editPhoto'])->name('fotografia.alterar');
        Route::post('/perfil/fotografia', [UserController::class, 'updatePhoto'])->name('fotografia.update');
    });


    // ---------------- MÓDULO DE MANUTENÇÃO ----------------
    Route::middleware(['perfil:administrador||gestor||tecnico_manutencao'])->group(function () {
        Route::resource('manutencoes', ManutencaoController::class);
    });

    Route::get('/manutencoes/export/pdf', [ManutencaoController::class, 'exportPdf'])
    ->name('manutencoes.export.pdf');

    Route::get('/manutencoes/export/excel', [ManutencaoController::class, 'exportExcel'])
    ->name('manutencoes.export.excel');


    // ---------------- MÓDULO DE REAVALIAÇÃO ----------------
    Route::middleware(['perfil:administrador||gestor||tecnico_contabilidade'])
        ->prefix('reavaliacoes')->name('reavaliacoes.')->group(function () {
        Route::get('/', [ReavaliacaoController::class, 'index'])->name('index');
        Route::get('/create', [ReavaliacaoController::class, 'create'])->name('create');
        Route::post('/', [ReavaliacaoController::class, 'store'])->name('store');
        Route::get('/{id}', [ReavaliacaoController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ReavaliacaoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReavaliacaoController::class, 'update'])->name('update');
        Route::delete('/{id}', [ReavaliacaoController::class, 'destroy'])->name('destroy');

        // Exportações
        Route::get('/export/excel', [ReavaliacaoController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [ReavaliacaoController::class, 'exportPdf'])->name('export.pdf');

        // Cálculo de depreciação
        Route::post('/calcular', [ReavaliacaoController::class, 'calcularDepreciacao'])->name('calcular');
    });

    // ---------------- API INTERNA ----------------
    Route::middleware('auth')->prefix('api')->group(function () {
        Route::get('/edificios/{provinciaId}', [ApiController::class, 'getEdificios'])->name('api.edificios');
        Route::get('/pisos/{edificioId}', [ApiController::class, 'getPisos'])->name('api.pisos');
        Route::get('/salas/{pisoId}', [ApiController::class, 'getSalas'])->name('api.salas');
        Route::get('/categorias/{grupoId}', [ApiController::class, 'getCategorias'])->name('api.categorias');
        Route::get('/subcategorias/{categoriaId}', [ApiController::class, 'getSubcategorias'])->name('api.subcategorias');
    });

    // ---------------- OUTRAS ROTAS ----------------
    Route::get('/salas-por-piso/{pisoId}', [SalaController::class, 'getSalasPorPiso'])->name('salas.por.piso');
    Route::get('/verificar-etiqueta/{etiqueta}', [BemController::class, 'verificarEtiqueta']);
});

// ======================== DADOS MESTRES ========================
Route::prefix('dados-mestres')->name('dados_mestres.')->group(function () {

    // Página principal
    Route::get('/', [DadosMestresController::class, 'index'])->name('index');

    // ---------------- CRUD PROVINCIAS ----------------
    Route::prefix('provincias')->name('provincias.')->group(function () {
        Route::get('/', [ProvinciaController::class, 'index'])->name('index');
        Route::get('/create', [ProvinciaController::class, 'create'])->name('create');
        Route::post('/', [ProvinciaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProvinciaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProvinciaController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProvinciaController::class, 'destroy'])->name('destroy');

        Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
            ->name('export.excel')
            ->defaults('section', 'provincias');

        Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
            ->name('export.pdf')
            ->defaults('section', 'provincias');
    });

    // ---------------- CRUD EDIFICIOS ----------------
    Route::prefix('edificios')->name('edificios.')->group(function () {
        Route::get('/', [EdificioController::class, 'index'])->name('index');
        Route::get('/create', [EdificioController::class, 'create'])->name('create');
        Route::post('/', [EdificioController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [EdificioController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EdificioController::class, 'update'])->name('update');
        Route::delete('/{id}', [EdificioController::class, 'destroy'])->name('destroy');

        // AJAX: edifícios por província
        Route::get('/por-provincia/{provinciaId}', [EdificioController::class, 'porProvincia'])->name('porProvincia');

        Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
            ->name('export.excel')
            ->defaults('section', 'edificios');

        Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
            ->name('export.pdf')
            ->defaults('section', 'edificios');
    });

    Route::prefix('pisos')->name('pisos.')->group(function ()
    {
        Route::get('/', [App\Http\Controllers\PisoController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\PisoController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PisoController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\PisoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\PisoController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\PisoController::class, 'destroy'])->name('destroy');

            Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
                ->name('export.excel')
                ->defaults('section', 'pisos');

            Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
                ->name('export.pdf')
                ->defaults('section', 'pisos');
    });

    // ==========================
// SALAS
// ==========================
    Route::prefix('salas')->name('salas.')->group(function () {
    Route::get('/', [App\Http\Controllers\SalaController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\SalaController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\SalaController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [App\Http\Controllers\SalaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\SalaController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\SalaController::class, 'destroy'])->name('destroy');

        Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
            ->name('export.excel')
            ->defaults('section', 'salas');

        Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
            ->name('export.pdf')
            ->defaults('section', 'salas');
    Route::get('/api/edificios/{provinciaId}', function ($provinciaId) {
    return \App\Models\Edificio::where('ProvinciaId', $provinciaId)
        ->orderBy('Nome')
        ->select('EdificioId', 'Nome')
        ->get();
    });

    Route::get('/api/pisos/{edificioId}', function ($edificioId) {
        return \App\Models\Piso::where('EdificioId', $edificioId)
            ->orderBy('Nome')
            ->select('PisoId', 'Nome')
            ->get();
    });

    Route::get('/verificar-duplicada', [App\Http\Controllers\SalaManipularController::class, 'verificarDuplicada'])->name('verificarDuplicada');


});



// ==========================
// SALAS (CRUD Web)
// ==========================
    Route::prefix('salas')->name('salas.')->group(function () {
    Route::get('/', [App\Http\Controllers\SalaManipularController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\SalaManipularController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\SalaManipularController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [App\Http\Controllers\SalaManipularController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\SalaManipularController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\SalaManipularController::class, 'destroy'])->name('destroy');
});



    Route::prefix('grupos')->name('grupos.')->group(function () {
    Route::get('/', [App\Http\Controllers\GrupoController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\GrupoController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\GrupoController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [App\Http\Controllers\GrupoController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\GrupoController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\GrupoController::class, 'destroy'])->name('destroy');

        Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
            ->name('export.excel')
            ->defaults('section', 'grupos');

        Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
            ->name('export.pdf')
            ->defaults('section', 'grupos');

    // (Opcional) Verificação AJAX para nome duplicado
    Route::get('/verificar-nome/{nome}', [App\Http\Controllers\GrupoController::class, 'verificarNome'])
        ->name('verificarNome');
    });

    Route::prefix('categorias')->name('categorias.')->group(function () {
    Route::get('/', [App\Http\Controllers\CategoriaController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\CategoriaController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\CategoriaController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [App\Http\Controllers\CategoriaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\CategoriaController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\CategoriaController::class, 'destroy'])->name('destroy');

        Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
            ->name('export.excel')
            ->defaults('section', 'categorias');

        Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
            ->name('export.pdf')
            ->defaults('section', 'categorias');


    // Verificação AJAX opcional
    Route::get('/verificar-duplicada', [App\Http\Controllers\CategoriaController::class, 'verificarDuplicada'])->name('verificarDuplicada');
    });


Route::prefix('subcategorias')->name('subcategorias.')->group(function () {
    // Página principal (lista + filtros)
    Route::get('/', [SubcategoriaController::class, 'index'])->name('index');

    // Formulário de criação
    Route::get('/create', [SubcategoriaController::class, 'create'])->name('create');

    // Armazenar nova subcategoria
    Route::post('/', [SubcategoriaController::class, 'store'])->name('store');

    // Formulário de edição
    Route::get('/{id}/edit', [SubcategoriaController::class, 'edit'])->name('edit');

    // Atualizar subcategoria existente
    Route::put('/{id}', [SubcategoriaController::class, 'update'])->name('update');

    // Excluir subcategoria
    Route::delete('/{id}', [SubcategoriaController::class, 'destroy'])->name('destroy');

        Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
            ->name('export.excel')
            ->defaults('section', 'subcategorias');

        Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
            ->name('export.pdf')
            ->defaults('section', 'subcategorias');

    Route::get('/por-categoria/{categoriaId}', [SubcategoriaController::class, 'subcategoriasPorCategoria'])
        ->name('porCategoria');
    });



    Route::prefix('estado-conservacao')
    ->name('estado_conservacao.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\EstadoConservacaoController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\EstadoConservacaoController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\EstadoConservacaoController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\EstadoConservacaoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\EstadoConservacaoController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\EstadoConservacaoController::class, 'destroy'])->name('destroy');

        Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
            ->name('export.excel')
            ->defaults('section', 'estado_conservacao');

        Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
            ->name('export.pdf')
            ->defaults('section', 'estado_conservacao');
    });


    // ---------------- CRUD MATERIAIS ----------------
    Route::prefix('materiais')
    ->name('materiais.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\MaterialController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\MaterialController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\MaterialController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\MaterialController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\MaterialController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\MaterialController::class, 'destroy'])->name('destroy');

        Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
            ->name('export.excel')
            ->defaults('section', 'materiais');

        Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
            ->name('export.pdf')
            ->defaults('section', 'materiais');
    });


    Route::prefix('condicoes_ambientais')
    ->name('condicoes_ambientais.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\CondicaoAmbientalController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\CondicaoAmbientalController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\CondicaoAmbientalController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\CondicaoAmbientalController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\CondicaoAmbientalController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\CondicaoAmbientalController::class, 'destroy'])->name('destroy');

        Route::get('/export/excel', [App\Http\Controllers\DadosMestresExportController::class, 'exportExcel'])
            ->name('export.excel')
            ->defaults('section', 'condicoes_ambientais');

        Route::get('/export/pdf', [App\Http\Controllers\DadosMestresExportController::class, 'exportPdf'])
            ->name('export.pdf')
            ->defaults('section', 'condicoes_ambientais');
    });

    });



Route::middleware('auth')->group(function () {
    Route::get('/senha/alterar', [SenhaController::class, 'edit'])->name('senha.alterar');
    Route::post('/senha/alterar', [SenhaController::class, 'update'])->name('senha.update');
});


Route::middleware(['auth'])->group(function() {
    // Inventário
    Route::get('inventario', [InventarioController::class,'index'])->name('inventario.index');
    Route::get('inventario/{bem}', [InventarioController::class,'show'])->name('inventario.show');
    Route::post('inventario/{bem}/update-status', [InventarioController::class,'updateStatus'])->name('inventario.updateStatus');
    Route::post('inventario/{bem}/update', [InventarioController::class,'update'])->name('inventario.update');
    Route::get('inventario/export/pdf', [InventarioController::class,'exportPDF'])->name('inventario.export.pdf');
    Route::get('inventario/export/excel', [InventarioController::class,'exportExcel'])->name('inventario.export.excel');

    // Ativos (Listagem)
    Route::get('ativos/export/pdf', [BemController::class, 'exportPdf'])->name('ativos.export.pdf');
    Route::get('ativos/export/excel', [BemController::class, 'exportExcel'])->name('ativos.export.excel');

    // Usuários (Gestão)
    Route::get('config/usuarios/export/pdf', [UserController::class, 'exportPdf'])->name('config.usuarios.export.pdf');
    Route::get('config/usuarios/export/excel', [UserController::class, 'exportExcel'])->name('config.usuarios.export.excel');


// ======================== MÓDULO DE FOLLOWUP ========================
Route::middleware(['auth', 'verified'])->prefix('followup')->name('followup.')->group(function () {

    // Página principal (com cascata)
    Route::get('/', [FollowUpController::class, 'index'])->name('index');

    // AJAX encadeado
    Route::get('/edificios/{provinciaId}', [FollowUpController::class, 'edificiosByProvincia'])->name('edificios');
    Route::get('/pisos/{edificioId}', [FollowUpController::class, 'pisosByEdificio'])->name('pisos');
    Route::get('/salas/{pisoId}', [FollowUpController::class, 'salasByPiso'])->name('salas');
    Route::get('/bens/{salaId}', [FollowUpController::class, 'bensBySala'])->name('bens');

    // Submissão de followup
    Route::post('/submit', [FollowUpController::class, 'submit'])->name('submit');

    // Relatórios e comparações
    Route::get('/relatorios', [FollowUpController::class, 'relatorios'])->name('relatorios');
    Route::get('/comparacao/{id}', [FollowUpController::class, 'comparacao'])->name('comparacao');
    Route::get('/export/{id}/{tipo}', [FollowUpController::class, 'export'])->name('export');
});


});

    // ---------------- EXPORT PDF/EXCEL INVENTÁRIO ----------------
Route::get('inventario/export/pdf', [InventarioController::class, 'exportPdf'])->name('inventario.export.pdf');
Route::get('inventario/export/excel', [InventarioController::class, 'exportExcel'])
     ->name('inventario.export.excel');


    // routes/web.php
Route::get('/ping-session', function() {
    return response()->json(['status' => 'ok']);
})->middleware('auth');


// ======================== AUTENTICAÇÃO ========================
require __DIR__.'/auth.php';
