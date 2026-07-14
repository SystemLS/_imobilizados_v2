<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $evento;
    public array $data;

    public function __construct(string $evento, array $data)
    {
        $this->evento = $evento;
        $this->data = $data;
    }
}
