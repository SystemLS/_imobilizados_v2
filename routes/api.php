<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\PisoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\BemController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\PowerBIController;
use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Http\Request;
use App\Models\Bem;

/**
 * @OA\Info(
 *     title="API Gestão de Ativos",
 *     version="1.0.0",
 *     description="API para integração e gestão de ativos, bem como exportações e webhooks"
 * )
 * @OA\Server(url="http://localhost:8000", description="Local Development Server")
 * @OA\Server(url="https://api.example.com", description="Production Server")
 */

// ============================================================================
// ROTAS PÚBLICAS (SEM AUTENTICAÇÃO)
// ============================================================================

/**
 * @OA\Get(
 *     path="/api/public/salas",
 *     summary="Listar todas as salas",
 *     tags={"Dados Mestres - Público"},
 *     @OA\Response(response=200, description="Lista de salas")
 * )
 */
Route::get('/public/salas', [SalaController::class, 'index']);

/**
 * @OA\Get(
 *     path="/api/public/edificios/{provinciaId}",
 *     summary="Listar edificios por província",
 *     tags={"Dados Mestres - Público"},
 *     @OA\Parameter(name="provinciaId", in="path", required=true, schema={"type": "integer"})
 * )
 */
Route::get('/public/edificios/{provinciaId}', [EdificioController::class, 'porProvincia']);

/**
 * @OA\Get(
 *     path="/api/public/pisos/{edificioId}",
 *     summary="Listar pisos por edifício",
 *     tags={"Dados Mestres - Público"},
 *     @OA\Parameter(name="edificioId", in="path", required=true, schema={"type": "integer"})
 * )
 */
Route::get('/public/pisos/{edificioId}', [PisoController::class, 'porEdificio']);

/**
 * @OA\Get(
 *     path="/api/public/categorias/{grupoId}",
 *     summary="Listar categorias por grupo",
 *     tags={"Dados Mestres - Público"},
 *     @OA\Parameter(name="grupoId", in="path", required=true, schema={"type": "integer"})
 * )
 */
Route::get('/public/categorias/{grupoId}', [CategoriaController::class, 'porGrupo']);

/**
 * @OA\Get(
 *     path="/api/public/subcategorias/categoria/{categoriaId}",
 *     summary="Listar subcategorias por categoria",
 *     tags={"Dados Mestres - Público"},
 *     @OA\Parameter(name="categoriaId", in="path", required=true, schema={"type": "integer"})
 * )
 */
Route::get('/public/subcategorias/categoria/{categoriaId}', [SubcategoriaController::class, 'subcategoriasPorCategoria']);



/**
 * @OA\Get(
 *     path="/api/public/followup/edificios/{provId}",
 *     summary="Listar edificios para Follow-up",
 *     tags={"Follow-up - Público"},
 *     @OA\Parameter(name="provId", in="path", required=true, schema={"type": "integer"})
 * )
 */
Route::get('/public/followup/edificios/{provId}', [FollowUpController::class, 'edificiosByProvincia']);

/**
 * @OA\Get(
 *     path="/api/public/followup/pisos/{edifId}",
 *     summary="Listar pisos para Follow-up",
 *     tags={"Follow-up - Público"},
 *     @OA\Parameter(name="edifId", in="path", required=true, schema={"type": "integer"})
 * )
 */
Route::get('/public/followup/pisos/{edifId}', [FollowUpController::class, 'pisosByEdificio']);

/**
 * @OA\Get(
 *     path="/api/public/followup/salas/{pisoId}",
 *     summary="Listar salas para Follow-up",
 *     tags={"Follow-up - Público"},
 *     @OA\Parameter(name="pisoId", in="path", required=true, schema={"type": "integer"})
 * )
 */
Route::get('/public/followup/salas/{pisoId}', [FollowUpController::class, 'salasByPiso']);

/**
 * @OA\Get(
 *     path="/api/public/followup/bens/{salaId}",
 *     summary="Listar bens para Follow-up",
 *     tags={"Follow-up - Público"},
 *     @OA\Parameter(name="salaId", in="path", required=true, schema={"type": "integer"})
 * )
 */
Route::get('/public/followup/bens/{salaId}', [FollowUpController::class, 'bensBySala']);

// ============================================================================
// ROTAS DE AUTENTICAÇÃO
// ============================================================================

/**
 * @OA\Post(
 *     path="/api/auth/login",
 *     summary="Fazer login e obter token",
 *     tags={"Autenticação"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Login bem-sucedido, retorna token"),
 *     @OA\Response(response=401, description="Credenciais inválidas")
 * )
 */
Route::post('/auth/login', [AuthenticationController::class, 'login']);

/**
 * @OA\Post(
 *     path="/api/auth/logout",
 *     summary="Fazer logout e revogar token",
 *     tags={"Autenticação"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(response=200, description="Logout bem-sucedido")
 * )
 */
Route::middleware('auth:sanctum')->post('/auth/logout', [AuthenticationController::class, 'logout']);

// ============================================================================
// ROTAS PROTEGIDAS (REQUEREM AUTENTICAÇÃO)
// ============================================================================

Route::middleware(['auth:sanctum'])->group(function () {

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Obter dados do usuário autenticado",
     *     tags={"Usuário"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Dados do usuário")
     * )
     */
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    // Power BI
    /**
     * @OA\Get(
     *     path="/api/powerbi/dados",
     *     summary="Retorna todos os dados para Power BI",
     *     tags={"PowerBI"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Dados retornados com sucesso")
     * )
     */
    Route::get('/powerbi/dados', [PowerBIController::class, 'dados']);

    /**
     * @OA\Get(
     *     path="/api/powerbi/{resource}",
     *     summary="Retorna dados de um recurso específico para Power BI",
     *     tags={"PowerBI"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="resource", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Dados retornados com sucesso"),
     *     @OA\Response(response=404, description="Recurso não encontrado")
     * )
     */
    Route::get('/powerbi/{resource}', [PowerBIController::class, 'resource']);

    // Dados Mestres com autenticação
    /**
     * @OA\Post(
     *     path="/api/salas",
     *     summary="Criar nova sala",
     *     tags={"Dados Mestres - Autenticado"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(required=true),
     *     @OA\Response(response=201, description="Sala criada com sucesso")
     * )
     */
    Route::post('/salas', [SalaController::class, 'store']);

    /**
     * @OA\Get(
     *     path="/api/bens-por-categoria/{id}",
     *     summary="Listar bens por categoria",
     *     tags={"Dados Mestres - Autenticado"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, schema={"type": "integer"})
     * )
     */
    Route::get('/bens-por-categoria/{id}', function ($id) {
        $bens = Bem::where('CategoriaId', $id)
            ->select('BemId', 'Etiqueta', 'Descricao', 'preco_aquisicao', 'data_aquisicao')
            ->orderBy('Descricao')
            ->get();

        return response()->json($bens);
    });

    /**
     * @OA\Post(
     *     path="/api/bens/{id}/export/pdf",
     *     summary="Exportar bem para PDF",
     *     tags={"Exportações"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, schema={"type": "integer"}),
     *     @OA\Response(response=200, description="PDF gerado com sucesso")
     * )
     */
    Route::post('/bens/{id}/export/pdf', [BemController::class, 'exportPdf']);

    /**
     * @OA\Post(
     *     path="/api/bens/{id}/export/excel",
     *     summary="Exportar bem para Excel",
     *     tags={"Exportações"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, schema={"type": "integer"}),
     *     @OA\Response(response=200, description="Excel gerado com sucesso")
     * )
     */
    Route::post('/bens/{id}/export/excel', [BemController::class, 'exportExcel']);

    // Webhooks
    /**
     * @OA\Post(
     *     path="/api/webhooks/register",
     *     summary="Registrar novo webhook",
     *     tags={"Webhooks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url","evento"},
     *             @OA\Property(property="url", type="string", format="url"),
     *             @OA\Property(property="evento", type="string", enum={"bem.criado","bem.atualizado","bem.deletado","manutencao.criada","reavaliacacao.criada"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Webhook registrado com sucesso")
     * )
     */
    Route::post('/webhooks/register', [\App\Http\Controllers\WebhookController::class, 'register']);

    /**
     * @OA\Get(
     *     path="/api/webhooks/list",
     *     summary="Listar webhooks registrados",
     *     tags={"Webhooks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Lista de webhooks")
     * )
     */
    Route::get('/webhooks/list', [\App\Http\Controllers\WebhookController::class, 'list']);

    /**
     * @OA\Delete(
     *     path="/api/webhooks/{id}",
     *     summary="Deletar webhook",
     *     tags={"Webhooks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, schema={"type": "integer"}),
     *     @OA\Response(response=200, description="Webhook deletado com sucesso")
     * )
     */
    Route::delete('/webhooks/{id}', [\App\Http\Controllers\WebhookController::class, 'delete']);
});

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login com email e password para obter um token JWT",
 *     name="Token",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 * )
 */
