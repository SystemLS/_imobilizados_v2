<?php

namespace App\Jobs;

use App\Models\Bem;
use App\Models\User;
use App\Exports\AtivosExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Bem $bem;
    protected User $user;
    protected string $format; // 'excel' ou 'pdf'

    public $tries = 3;
    public $timeout = 300; // 5 minutos

    public function __construct(Bem $bem, User $user, string $format = 'excel')
    {
        $this->bem = $bem;
        $this->user = $user;
        $this->format = $format;
    }

    public function handle(): void
    {
        try {
            $filename = "bem_{$this->bem->BemId}_{$this->format}." . ($this->format === 'excel' ? 'xlsx' : 'pdf');
            $path = "exports/{$filename}";

            if ($this->format === 'excel') {
                Excel::store(
                    new AtivosExport([$this->bem->BemId]),
                    $path,
                    'local'
                );
            } else {
                // Para PDF, usar o método existente
                // Esta é uma simplificação - você pode expandir conforme necessário
            }

            // Disparar evento de sucesso
            event(new \App\Events\WebhookEvent('export.concluida', [
                'bem_id' => $this->bem->BemId,
                'user_id' => $this->user->id,
                'formato' => $this->format,
                'arquivo' => $filename,
                'url_download' => Storage::url($path),
            ]));

            // TODO: Notificar usuário por email com link de download
        } catch (\Exception $e) {
            // Disparar evento de erro
            event(new \App\Events\WebhookEvent('export.erro', [
                'bem_id' => $this->bem->BemId,
                'user_id' => $this->user->id,
                'formato' => $this->format,
                'erro' => $e->getMessage(),
            ]));

            throw $e;
        }
    }

    public function backoff(): array
    {
        return [60, 300, 600]; // retry com delays
    }
}
