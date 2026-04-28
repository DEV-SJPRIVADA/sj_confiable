<?php

declare(strict_types=1);

namespace App\Domain\Routing;

use App\Domain\Enums\UserRole;
use App\Models\Usuario;
use InvalidArgumentException;

/**
 * Ruta "home" por rol (equivalente a la lógica de index.php del sistema legado).
 */
final class RoleHome
{
    public static function pathFor(Usuario $usuario): string
    {
        $role = UserRole::tryFrom((int) $usuario->id_rol);
        if ($role === null) {
            throw new InvalidArgumentException('Rol de usuario desconocido: '.$usuario->id_rol);
        }

        return match ($role) {
            UserRole::Admin,
            UserRole::SuperAdmin => '/panel/consultor/inicio',
            UserRole::Cliente,
            UserRole::AdminCliente,
            UserRole::ClienteSinPermisos => '/panel/cliente/inicio',
            UserRole::Proveedor => '/panel/proveedor/solicitudes',
        };
    }
}
