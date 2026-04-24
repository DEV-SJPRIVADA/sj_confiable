<?php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Roles reales (t_cat_roles.id_rol) del sistema en producción.
 * No reordenar valores: se persisten en t_usuarios.
 */
enum UserRole: int
{
    case Cliente = 1;
    case Admin = 2;
    case SuperAdmin = 3;
    case AdminCliente = 4;
    case ClienteSinPermisos = 5;
    case Proveedor = 6;

    public function label(): string
    {
        return match ($this) {
            self::Cliente => 'cliente',
            self::Admin => 'admin',
            self::SuperAdmin => 'SuperAdmin',
            self::AdminCliente => 'admin_cliente',
            self::ClienteSinPermisos => 'cliente_sin_p',
            self::Proveedor => 'Proveedores',
        };
    }

    public function isSJStaff(): bool
    {
        return in_array($this, [self::Admin, self::SuperAdmin], true);
    }

    public function isClienteSide(): bool
    {
        return in_array($this, [self::Cliente, self::AdminCliente, self::ClienteSinPermisos], true);
    }
}
