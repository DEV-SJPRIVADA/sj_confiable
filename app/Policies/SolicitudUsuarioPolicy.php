<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SolicitudUsuario;
use App\Models\Usuario;
use App\Policies\Concerns\AuthorizesSJStaff;

class SolicitudUsuarioPolicy
{
    use AuthorizesSJStaff;

    public function viewAny(Usuario $actor): bool
    {
        return $this->isSJConsultor($actor);
    }

    public function respond(Usuario $actor, SolicitudUsuario $solicitud): bool
    {
        if (! $this->isSJConsultor($actor)) {
            return false;
        }
        if ($solicitud->estado !== 'Pendiente') {
            return false;
        }

        return true;
    }
}
