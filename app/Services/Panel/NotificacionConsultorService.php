<?php

declare(strict_types=1);

namespace App\Services\Panel;

use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class NotificacionConsultorService
{
    /**
     * @return Collection<int, Notificacion>
     */
    public function listarParaUsuario(Usuario $usuario): Collection
    {
        $idRol = (int) $usuario->id_rol;
        if (! in_array($idRol, [2, 3], true)) {
            return new Collection;
        }

        return Notificacion::query()
            ->forRol($idRol)
            ->noLeidas()
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(250)
            ->get();
    }

    public function contarNoLeidas(Usuario $usuario): int
    {
        $idRol = (int) $usuario->id_rol;
        if (! in_array($idRol, [2, 3], true)) {
            return 0;
        }

        return Notificacion::query()
            ->forRol($idRol)
            ->noLeidas()
            ->count();
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function marcarLeidas(Usuario $usuario, array $ids, bool $todas): int
    {
        $idRol = (int) $usuario->id_rol;
        if (! in_array($idRol, [2, 3], true)) {
            return 0;
        }

        return (int) DB::transaction(function () use ($idRol, $ids, $todas): int {
            if ($todas) {
                return Notificacion::query()->forRol($idRol)->update(['leido' => 1]);
            }
            if ($ids === []) {
                return 0;
            }

            $idsLimpio = array_values(array_unique(array_map('intval', $ids)));

            return Notificacion::query()
                ->forRol($idRol)
                ->whereIn('id', $idsLimpio)
                ->update(['leido' => 1]);
        });
    }

    public function urlDetalle(Notificacion $n): string
    {
        $cn = 'cn='.(int) $n->id;

        if ($n->tipo === 'usuarios') {
            return route('panel.consultor.solicitudes-usuarios.index').'?'.$cn;
        }

        return route('panel.consultor.solicitudes.show', $n->id_solicitud).'?'.$cn;
    }

    /**
     * Al abrir el enlace desde la campanita: marca leída sólo si la notificación
     * pertenece al rol del usuario y, si se indica, coincide con la solicitud esperada.
     */
    public function marcarLeidaAlSeguirEnlace(Usuario $usuario, int $notificacionId, ?int $solicitudIdEsperada): void
    {
        if ($notificacionId <= 0) {
            return;
        }

        $idRol = (int) $usuario->id_rol;
        if (! in_array($idRol, [2, 3], true)) {
            return;
        }

        $query = Notificacion::query()->forRol($idRol)->whereKey($notificacionId);
        $notif = $query->first();
        if ($notif === null) {
            return;
        }

        if ($solicitudIdEsperada !== null && (int) $notif->id_solicitud !== $solicitudIdEsperada) {
            return;
        }

        if (! $notif->leido) {
            Notificacion::query()->whereKey($notificacionId)->update(['leido' => 1]);
        }
    }
}
