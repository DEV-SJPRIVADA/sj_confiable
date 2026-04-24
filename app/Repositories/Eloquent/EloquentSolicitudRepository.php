<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Solicitud;
use App\Repositories\Contracts\SolicitudRepository;
use Illuminate\Support\Collection;

final class EloquentSolicitudRepository implements SolicitudRepository
{
    public function listActiveForConsultor(int $activo = 1): Collection
    {
        return $this->baseListQuery($activo)->get();
    }

    public function listActiveForProveedor(int $idProveedor, int $activo = 1): Collection
    {
        return $this->baseListQuery($activo)
            ->where('solicitudes.id_proveedor', $idProveedor)
            ->get();
    }

    public function listActiveForClienteOrganization(int $idCliente, int $activo = 1): Collection
    {
        return $this->baseListQuery($activo)
            ->whereHas('creador', fn ($q) => $q->where('id_cliente', $idCliente))
            ->get();
    }

    public function findOrFail(int $id): Solicitud
    {
        return Solicitud::query()
            ->with(['creador.cliente', 'creador', 'proveedorAsignado', 'servicio', 'serviciosPivote', 'paquete'])
            ->findOrFail($id);
    }

    public function findForDetalle(int $id): Solicitud
    {
        return Solicitud::query()
            ->with([
                'creador.cliente',
                'creador',
                'proveedorAsignado',
                'servicio',
                'serviciosPivote',
                'paquete',
                'documentos',
                'evaluados',
                'historialRespuestas.usuario.proveedor',
                'ultimaRespuestaMadre',
            ])
            ->findOrFail($id);
    }

    /**
     * Equivalente a `GestionSolicitud::obtenerSolicitudes` (conteos y relaciones).
     */
    private function baseListQuery(int $activo): \Illuminate\Database\Eloquent\Builder
    {
        return Solicitud::query()
            ->where('solicitudes.activo', $activo)
            ->with(['creador.cliente', 'servicio', 'serviciosPivote'])
            ->withCount(['documentos', 'evaluados'])
            ->orderByDesc('solicitudes.fecha_creacion');
    }
}
