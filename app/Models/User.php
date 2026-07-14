<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\CustomVerifyEmail;


class User extends Authenticatable implements MustVerifyEmail
{


    use HasFactory, Notifiable;

    /**
     * Atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'perfil',
        'fotografia', // controle de perfis
    ];

    /**
     * Atributos ocultos para arrays ou JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos que devem ser convertidos para tipos específicos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Envia a notificação personalizada de reset de senha.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Envia a notificação personalizada de verificação de e-mail.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

    /**
     * Perfis válidos do sistema.
     *
     * @return array
     */
    public static function perfisValidos(): array
    {
        return [
            'padrao',
            'tecnico_manutencao',
            'tecnico_contabilidade',
            'tecnico_cadastro',
            'gestor',
            'administrador',
        ];
    }

    /**
     * Verifica se o usuário é administrador.
     *
     * @return bool
     */
    public function isAdministrador(): bool
    {
        return $this->perfil === 'administrador';
    }

    /**
     * Verifica se o usuário é gestor patrimonial.
     *
     * @return bool
     */
    public function isGestor(): bool
    {
        return $this->perfil === 'gestor';
    }

    /**
     * Verifica se o usuário é técnico (qualquer tipo de técnico).
     *
     * @return bool
     */
    public function isTecnico(): bool
    {
        return in_array($this->perfil, [
            'tecnico_cadastro',
            'tecnico_contabilidade',
            'tecnico_manutencao',
        ]);
    }

    /**
     * Verifica se o usuário tem perfil padrão.
     *
     * @return bool
     */
    public function isPadrao(): bool
    {
        return $this->perfil === 'padrao';
    }
}
