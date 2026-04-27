<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Solicitud;
use App\Repositories\Contracts\SolicitudRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class EloquentSolicitudRepository implements SolicitudRepository
{
    public function listActiveForConsultor(int $activo = 1): Collection
    {
        return $this->baseListQuery($activo)
            ->orderByDesc('solicitudes.fecha_creacion')
            ->get();
    }

    public function paginateForConsultor(
        int $activo,
        int $perPage,
        string $q,
        string $sort,
        string $dir
    ): LengthAwarePaginator {
        $query = $this->baseListQuery($activo);
        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function (Builder $w) use ($like, $q): void {
                $w->where('solicitudes.nombres', 'like', $like)
                    ->orWhere('solicitudes.apellidos', 'like', $like)
                    ->orWhere('solicitudes.numero_documento', 'like', $like)
                    ->orWhere('solicitudes.tipo_identificacion', 'like', $like)
                    ->orWhere('solicitudes.ciudad_solicitud_servicio', 'like', $like)
                    ->orWhere('solicitudes.ciudad_residencia_evaluado', 'like', $like)
                    ->orWhere('solicitudes.estado', 'like', $like)
                    ->orWhereHas('creador', function (Builder $c) use ($like): void {
                        $c->where('t_usuarios.usuario', 'like', $like);
                    })
                    ->orWhereHas('creador.cliente', function (Builder $c) use ($like): void {
                        $c->where('t_clientes.razon_social', 'like', $like);
                    })
                    ->orWhereHas('creador.persona', function (Builder $c) use ($like): void {
                        $c->where('t_persona.correo', 'like', $like);
                    });
                if (preg_match('/^\d+$/', $q) === 1) {
                    $w->orWhere('solicitudes.id', (int) $q);
                }
            });
        }
        $this->applyGestionSort($query, $sort, $dir);

        return $query
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();
    }

    private function applyGestionSort(Builder $query, string $sort, string $dir): void
    {
        $dir = $dir === 'desc' ? 'desc' : 'asc';
        if ($sort === 'cliente') {
            $sub = '(
                SELECT t_clientes.razon_social
                FROM t_usuarios
                INNER JOIN t_clientes ON t_clientes.id_cliente = t_usuarios.id_cliente
                WHERE t_usuarios.id_usuario = solicitudes.usuario_id
                LIMIT 1
            )';
            $query->orderByRaw("COALESCE($sub, '') ".$dir);
        } elseif ($sort === 'enviado') {
            $sub = '(
                SELECT COALESCE(t_persona.correo, t_usuarios.usuario)
                FROM t_usuarios
                LEFT JOIN t_persona ON t_persona.id_persona = t_usuarios.id_persona
                WHERE t_usuarios.id_usuario = solicitudes.usuario_id
                LIMIT 1
            )';
            $query->orderByRaw("COALESCE($sub, '') ".$dir);
        } elseif ($sort === 'evaluado') {
            $query->orderBy('solicitudes.nombres', $dir)
                ->orderBy('solicitudes.apellidos', $dir);
        } elseif (in_array($sort, [
            'id',
            'documento',
            'ciudad',
            'fecha',
            'estado',
        ], true)) {
            $map = [
                'id' => 'solicitudes.id',
                'documento' => 'solicitudes.numero_documento',
                'ciudad' => 'solicitudes.ciudad_solicitud_servicio',
                'fecha' => 'solicitudes.fecha_creacion',
                'estado' => 'solicitudes.estado',
            ];
            $query->orderBy($map[$sort], $dir);
        } else {
            $query->orderByDesc('solicitudes.fecha_creacion');
        }
        if ($sort !== 'id') {
            $query->orderBy('solicitudes.id', 'desc');
        }
    }

    public function listActiveForProveedor(int $idProveedor, int $activo = 1): Collection
    {
        return $this->baseListQuery($activo)
            ->where('solicitudes.id_proveedor', $idProveedor)
            ->orderByDesc('solicitudes.fecha_creacion')
            ->get();
    }

    public function listActiveForClienteOrganization(int $idCliente, int $activo = 1): Collection
    {
        return $this->baseListQuery($activo)
            ->whereHas('creador', fn ($q) => $q->where('id_cliente', $idCliente))
            ->orderByDesc('solicitudes.fecha_creacion')
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
            ->with(['creador.cliente', 'creador.persona', 'servicio', 'serviciosPivote', 'paquete', 'proveedorAsignado', 'documentos'])
            ->withCount(['documentos', 'evaluados']);
    }
}
