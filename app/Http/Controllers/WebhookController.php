<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebhookController extends Controller
{
    /**
     * Registrar novo webhook
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'evento' => 'required|string|in:bem.criado,bem.atualizado,bem.deletado,manutencao.criada,reavaliacacao.criada',
        ]);

        $webhook = Webhook::create([
            'user_id' => Auth::id(),
            'url' => $validated['url'],
            'evento' => $validated['evento'],
            'ativo' => true,
            'tentativas_falhas' => 0,
        ]);

        return response()->json([
            'message' => 'Webhook registrado com sucesso',
            'webhook' => $webhook,
        ], 201);
    }

    /**
     * Listar webhooks do usuário
     */
    public function list(Request $request)
    {
        $webhooks = Webhook::where('user_id', Auth::id())
            ->get();

        return response()->json([
            'webhooks' => $webhooks,
        ], 200);
    }

    /**
     * Deletar webhook
     */
    public function delete(Request $request, $id)
    {
        $webhook = Webhook::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $webhook->delete();

        return response()->json([
            'message' => 'Webhook deletado com sucesso',
        ], 200);
    }
}
