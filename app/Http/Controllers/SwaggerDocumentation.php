<?php

/**
 * @OA\Info(
 *   title="API Gestão de Ativos",
 *   version="1.0.0",
 *   description="API para integração e gestão de ativos, bem como exportações e webhooks",
 *   @OA\Contact(
 *     name="Suporte",
 *     email="support@example.com"
 *   ),
 *   @OA\License(
 *     name="MIT",
 *     identifier="MIT"
 *   )
 * )
 *
 * @OA\Server(
 *   url="http://localhost:8000",
 *   description="Local Development Server"
 * )
 *
 * @OA\Server(
 *   url="https://api.example.com",
 *   description="Production Server"
 * )
 *
 * @OA\SecurityScheme(
 *   type="http",
 *   description="Login com email e password para obter um token",
 *   name="Token",
 *   in="header",
 *   scheme="bearer",
 *   bearerFormat="JWT",
 *   securityScheme="bearerAuth"
 * )
 */

namespace App\Http\Controllers;

class SwaggerDocumentation {}

