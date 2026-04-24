<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Domain\Enums\UserRole;
use App\Models\Usuario;

trait AuthorizesSJStaff
{
    private function isSJConsultor(Usuario $usuario): bool
    {
        $rol = UserRole::tryFrom((int) $usuario->id_rol);

        return in_array($rol, [UserRole::Admin, UserRole::SuperAdmin], true);
    }
}
