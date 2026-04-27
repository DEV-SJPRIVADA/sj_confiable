<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Enums\UserRole;
use App\Models\Usuario;
use App\Policies\Concerns\AuthorizesSJStaff;

/**
 * Alta/edición de cuentas en catálogo (equivalente a gestión del legado, no "perfil propio" cliente).
 */
class UsuarioPolicy
{
    use AuthorizesSJStaff;

    public function viewAny(Usuario $actor): bool
    {
        return $this->isSJConsultor($actor);
    }

    public function create(Usuario $actor): bool
    {
        return $this->isSJConsultor($actor);
    }

    public function update(Usuario $actor, Usuario $target): bool
    {
        if (! $this->isSJConsultor($actor)) {
            return false;
        }

        if ($this->isSuperAdmin($actor)) {
            return true;
        }

        // Admin (2): no edita a SuperAdmin, ni su propia cuenta por este módulo, no asigna roles de administración SJ.
        if ($this->isAdminSolo($actor)) {
            if ((int) $actor->id_usuario === (int) $target->id_usuario) {
                return false;
            }
            if ($this->isSuperAdmin($target)) {
                return false;
            }
        }

        return true;
    }

    public function assignRoleOnCreate(Usuario $actor, int $newRolId): bool
    {
        if (! $this->isSJConsultor($actor)) {
            return false;
        }
        if ($this->isSuperAdmin($actor)) {
            return true;
        }
        if ($this->isAdminSolo($actor) && in_array($newRolId, [2, 3], true)) {
            return false;
        }

        return true;
    }

    public function assignRoleOnUpdate(Usuario $actor, Usuario $target, int $newRolId): bool
    {
        if (! $this->update($actor, $target)) {
            return false;
        }
        if ($this->isSuperAdmin($actor)) {
            return true;
        }
        if ($this->isAdminSolo($actor) && in_array($newRolId, [2, 3], true)) {
            return false;
        }

        return true;
    }

    public function toggleActivo(Usuario $actor, Usuario $target): bool
    {
        return $this->update($actor, $target);
    }

    private function isSuperAdmin(Usuario $usuario): bool
    {
        return (int) $usuario->id_rol === UserRole::SuperAdmin->value;
    }

    private function isAdminSolo(Usuario $usuario): bool
    {
        return (int) $usuario->id_rol === UserRole::Admin->value;
    }
}
