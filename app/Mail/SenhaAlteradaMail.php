<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SenhaAlteradaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user; // Usuário logado

    /**
     * Construtor recebe o usuário logado
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build da mensagem
     */
    public function build()
    {
        return $this
            ->subject('Confirmação de Alteração de Senha')
            ->markdown('emails.senha_alterada')
            ->with([
                'nome' => $this->user->name,
                'email' => $this->user->email,
            ]);
    }
}
