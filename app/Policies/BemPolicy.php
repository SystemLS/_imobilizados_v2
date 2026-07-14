<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Bem;
use Illuminate\Auth\Access\HandlesAuthorization;

class BemPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Bem $bem)
    {
        // Aqui você define a regra de autorização
        // Exemplo: somente usuários admin podem atualizar
        return $user->is_admin; // ou outra lógica
    }
}
