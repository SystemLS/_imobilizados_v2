<?php

namespace App\Listeners;

use App\Events\WebhookEvent;
use App\Models\Webhook;
use App\Jobs\DispatchWebhookJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWebhookNotification implements ShouldQueue
{
    public function handle(WebhookEvent $event): void
    {
        // Encontrar webhooks que devem ser disparados
        $webhooks = Webhook::where('evento', $event->evento)
            ->where('ativo', true)
            ->get();

        foreach ($webhooks as $webhook) {
            // Despachar job para fila
            DispatchWebhookJob::dispatch($webhook, $event->data);
        }
    }
}
