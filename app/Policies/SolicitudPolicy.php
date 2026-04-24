<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Enums\UserRole;
use App\Models\Solicitud;
use App\Models\Usuario;

/**
 * Reglas alineadas al flujo: cliente nunca trata con el asociado de negocios de forma directa
 * (la mediación es por consultor SJ; la comprobación de “canal” en mensajes/respuestas
 * se refuerza al portar módulos de notificaciones y respuestas).
 */
class SolicitudPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return UserRole::tryFrom((int) $usuario->id_rol) !== null;
    }

    public function view(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);
        if ($rol === null) {
            return false;
        }

        return match ($rol) {
            UserRole::Admin, UserRole::SuperAdmin => true,
            UserRole::Proveedor => (int) $solicitud->id_proveedor === (int) $actor->id_proveedor
                && $actor->id_proveedor !== null,
            UserRole::Cliente, UserRole::AdminCliente, UserRole::ClienteSinPermisos => $this
                ->solicitudPerteneceAOrganizacionCliente($solicitud, $actor),
        };
    }

    /**
     * Cierre / estado final: solo personal SJ (consultor).
     */
    public function closeAsConsultor(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);

        return in_array($rol, [UserRole::Admin, UserRole::SuperAdmin], true) && $this->view($actor, $solicitud);
    }

    public function manageAsConsultor(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);

        return in_array($rol, [UserRole::Admin, UserRole::SuperAdmin], true) && $this->view($actor, $solicitud);
    }

    public function assignToProveedor(Usuario $actor, Solicitud $solicitud): bool
    {
        return $this->manageAsConsultor($actor, $solicitud);
    }

    public function actAsProveedor(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);

        return $rol === UserRole::Proveedor
            && (int) $solicitud->id_proveedor === (int) $actor->id_proveedor
            && $actor->id_proveedor !== null;
    }

    private function solicitudPerteneceAOrganizacionCliente(Solicitud $solicitud, Usuario $actor): bool
    {
        if ($actor->id_cliente === null) {
            return false;
        }

        $creador = $solicitud->relationLoaded('creador') ? $solicitud->creador : $solicitud->creador()->first();
        if ($creador === null) {
            return false;
        }

        return (int) $creador->id_cliente === (int) $actor->id_cliente;
    }
}
