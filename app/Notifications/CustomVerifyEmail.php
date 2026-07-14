<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmailBase
{
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
                    ->subject('🎉 Ative sua conta no Sistema de Gestão de Ativos')
                    ->greeting('Olá '.$notifiable->name.'! 👋')
                    ->line('Sua conta foi criada com sucesso no Sistema de Gestão de Ativos.')
                    ->line('Para começar a usar o sistema, você precisa ativar sua conta clicando no botão abaixo:')
                    ->action('✅ Ativar Conta', $url)
                    ->line('Após a ativação, você poderá fazer login e começar a gerenciar seus ativos.')
                    ->line('Se você não criou esta conta, pode ignorar este e-mail com segurança.')
                    ->salutation('Atenciosamente, Equipe de Suporte do Sistema de Gestão de Ativos');
    }
}
