<?php

declare(strict_types=1);

namespace App\Services\Panel;

use App\Models\NotificacionProveedor;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class NotificacionProveedorService
{
    /**
     * @return Collection<int, NotificacionProveedor>
     */
    public function listarParaUsuario(Usuario $usuario): Collection
    {
        if ((int) $usuario->id_rol !== 6 || $usuario->id_proveedor === null) {
            return new Collection;
        }

        return NotificacionProveedor::query()
            ->where('id_proveedor_destino', (int) $usuario->id_proveedor)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(250)
            ->get();
    }

    public function contarNoLeidas(Usuario $usuario): int
    {
        if ((int) $usuario->id_rol !== 6 || $usuario->id_proveedor === null) {
            return 0;
        }

        return NotificacionProveedor::query()
            ->where('id_proveedor_destino', (int) $usuario->id_proveedor)
            ->where('leido', false)
            ->count();
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function marcarLeidas(Usuario $usuario, array $ids, bool $todas): int
    {
        if ((int) $usuario->id_rol !== 6 || $usuario->id_proveedor === null) {
            return 0;
        }

        $idProv = (int) $usuario->id_proveedor;

        return (int) DB::transaction(function () use ($idProv, $ids, $todas): int {
            $base = NotificacionProveedor::query()->where('id_proveedor_destino', $idProv);

            if ($todas) {
                return $base->where('leido', false)->update(['leido' => true]);
            }
            if ($ids === []) {
                return 0;
            }

            $idsLimpio = array_values(array_unique(array_map('intval', $ids)));

            return $base
                ->whereIn('id', $idsLimpio)
                ->where('leido', false)
                ->update(['leido' => true]);
        });
    }

    public function urlDetalle(NotificacionProveedor $n): string
    {
        return route('panel.proveedor.solicitudes.show', $n->id_solicitud);
    }
}
