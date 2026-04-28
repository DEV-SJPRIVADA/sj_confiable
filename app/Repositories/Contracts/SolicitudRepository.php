<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Solicitud;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SolicitudRepository
{
    /**
     * Listado global para consultores SJ (roles 2 y 3).
     *
     * @return Collection<int, Solicitud>
     */
    public function listActiveForConsultor(int $activo = 1): Collection;

    /**
     * Listado paginado para el panel consultor (gestión de solicitudes) con búsqueda y orden.
     */
    public function paginateForConsultor(
        int $activo,
        int $perPage,
        string $q,
        string $sort,
        string $dir
    ): LengthAwarePaginator;

    /**
     * Listado para asociado de negocios (rol 6): solo solicitudes asignadas a su id_proveedor.
     *
     * @return Collection<int, Solicitud>
     */
    public function listActiveForProveedor(int $idProveedor, int $activo = 1): Collection;

    /**
     * Listado para usuarios de la misma organización cliente (roles 1, 4, 5).
     *
     * @return Collection<int, Solicitud>
     */
    public function listActiveForClienteOrganization(int $idCliente, int $activo = 1): Collection;

    public function findOrFail(int $id): Solicitud;

    /**
     * Detalle con historial y documentos (port GestionSolicitud / detalle).
     */
    public function findForDetalle(int $id): Solicitud;

    /**
     * Vista Estado de solicitud (legado ResultadoSolicitud): detalle + documentos de respuesta + historial.
     */
    public function findForEstadoCliente(int $id): Solicitud;
}
