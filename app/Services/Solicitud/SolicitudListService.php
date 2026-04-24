<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Domain\Enums\UserRole;
use App\Models\Usuario;
use App\Repositories\Contracts\SolicitudRepository;
use Illuminate\Support\Collection;

/**
 * Orquesta listados según rol; no expone datos fuera del alcance del actor.
 */
final class SolicitudListService
{
    public function __construct(
        private readonly SolicitudRepository $solicitudes,
    ) {}

    /**
     * @return Collection<int, \App\Models\Solicitud>
     */
    public function forActor(Usuario $actor): Collection
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);
        if ($rol === null) {
            return collect();
        }

        return match ($rol) {
            UserRole::Admin,
            UserRole::SuperAdmin => $this->solicitudes->listActiveForConsultor(),
            UserRole::Proveedor => $this->listForProveedorActor($actor),
            UserRole::Cliente,
            UserRole::AdminCliente,
            UserRole::ClienteSinPermisos => $this->listForClienteActor($actor),
        };
    }

    /**
     * @return Collection<int, \App\Models\Solicitud>
     */
    private function listForProveedorActor(Usuario $actor): Collection
    {
        if ($actor->id_proveedor === null) {
            return collect();
        }

        return $this->solicitudes->listActiveForProveedor((int) $actor->id_proveedor);
    }

    /**
     * @return Collection<int, \App\Models\Solicitud>
     */
    private function listForClienteActor(Usuario $actor): Collection
    {
        if ($actor->id_cliente === null) {
            return collect();
        }

        return $this->solicitudes->listActiveForClienteOrganization((int) $actor->id_cliente);
    }
}
