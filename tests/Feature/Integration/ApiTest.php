<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use App\Models\Webhook;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar usuário de teste
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Teste: Login com sucesso
     */
    public function test_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'email', 'name'],
                'token',
                'token_type',
            ]);

        $this->assertNotNull($response->json('token'));
    }

    /**
     * Teste: Login com credenciais inválidas
     */
    public function test_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Credenciais inválidas']);
    }

    /**
     * Teste: Logout revoga token
     */
    public function test_logout_revokes_token()
    {
        // Login
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        // Logout
        $logoutResponse = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/logout');

        $logoutResponse->assertStatus(200);

        // Tentar usar o token deve falhar
        $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user')
            ->assertStatus(401);
    }

    /**
     * Teste: Acessar endpoint autenticado sem token
     */
    public function test_access_authenticated_endpoint_without_token()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /**
     * Teste: Acessar endpoint autenticado com token válido
     */
    public function test_access_authenticated_endpoint_with_valid_token()
    {
        // Login
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        // Acessar endpoint protegido
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $this->user->id,
                'email' => 'test@example.com',
            ]);
    }

    /**
     * Teste: PowerBI endpoint retorna dados JSON do banco
     */
    public function test_powerbi_endpoint_returns_database_json()
    {
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/powerbi/dados');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'usuarios',
                'bens',
                'manutencoes',
                'reavaliacoes',
                'salas',
                'pisos',
                'edificios',
                'provincias',
                'categorias',
                'subcategorias',
                'grupos',
                'inventarios',
            ]);
    }
}

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Obter token
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $this->token = $response->json('token');
    }

    /**
     * Teste: Registrar webhook com sucesso
     */
    public function test_register_webhook_successfully()
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/webhooks/register', [
                'url' => 'https://example.com/webhook',
                'evento' => 'bem.criado',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'webhook' => [
                    'id',
                    'user_id',
                    'url',
                    'evento',
                    'ativo',
                    'tentativas_falhas',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseHas('webhooks', [
            'user_id' => $this->user->id,
            'url' => 'https://example.com/webhook',
            'evento' => 'bem.criado',
        ]);
    }

    /**
     * Teste: Registrar webhook com URL inválida
     */
    public function test_register_webhook_with_invalid_url()
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/webhooks/register', [
                'url' => 'not-a-url',
                'evento' => 'bem.criado',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('url');
    }

    /**
     * Teste: Listar webhooks
     */
    public function test_list_webhooks()
    {
        // Criar webhooks de teste
        Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/webhook1',
            'evento' => 'bem.criado',
        ]);

        Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/webhook2',
            'evento' => 'bem.atualizado',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/webhooks/list');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'webhooks');
    }

    /**
     * Teste: Deletar webhook
     */
    public function test_delete_webhook()
    {
        $webhook = Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/webhook',
            'evento' => 'bem.criado',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/webhooks/{$webhook->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('webhooks', [
            'id' => $webhook->id,
        ]);
    }

    /**
     * Teste: Não permitir deletar webhook de outro usuário
     */
    public function test_cannot_delete_other_user_webhook()
    {
        $otherUser = User::factory()->create();

        $webhook = Webhook::create([
            'user_id' => $otherUser->id,
            'url' => 'https://example.com/webhook',
            'evento' => 'bem.criado',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/webhooks/{$webhook->id}");

        $response->assertStatus(404);
    }
}

class CorsTest extends TestCase
{
    /**
     * Teste: CORS headers presentes
     */
    public function test_cors_headers_present()
    {
        $response = $this->getJson('/api/public/salas', [
            'Origin' => 'http://localhost:3000',
        ]);

        $response->assertHeader('Access-Control-Allow-Origin');
        $response->assertHeader('Access-Control-Allow-Methods');
    }

    /**
     * Teste: Preflight request
     */
    public function test_preflight_request()
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:3000',
            'Access-Control-Request-Method' => 'POST',
        ])->options('/api/webhooks/register');

        $response->assertHeader('Access-Control-Allow-Origin');
        $response->assertStatus(200);
    }
}
