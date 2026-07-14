<?php

namespace App\Jobs;

use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Webhook $webhook;
    protected array $data;

    public function __construct(Webhook $webhook, array $data)
    {
        $this->webhook = $webhook;
        $this->data = $data;
    }

    public function handle(): void
    {
        try {
            $response = Http::timeout(30)
                ->post($this->webhook->url, [
                    'evento' => $this->webhook->evento,
                    'timestamp' => now()->toIso8601String(),
                    'data' => $this->data,
                ]);

            if ($response->failed()) {
                throw new \Exception("Webhook retornou status {$response->status()}");
            }

            // Resetar contador de falhas
            $this->webhook->update([
                'tentativas_falhas' => 0,
                'ultima_tentativa' => now(),
            ]);
        } catch (\Exception $e) {
            $this->webhook->increment('tentativas_falhas');
            $this->webhook->update(['ultima_tentativa' => now()]);

            // Desativar webhook após 10 tentativas falhas
            if ($this->webhook->tentativas_falhas >= 10) {
                $this->webhook->update(['ativo' => false]);
            }

            // Se ainda temos retries, relançar job
            if ($this->attempts() < 5) {
                $this->release(60); // Aguardar 60 segundos antes de retentar
            }
        }
    }

    public function backoff(): array
    {
        return [10, 30, 60, 300, 600]; // Delays exponenciais em segundos
    }

    public function maxExceptions(): int
    {
        return 5; // Máximo de exceções antes de falhar
    }
}
