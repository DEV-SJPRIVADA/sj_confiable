<?php

declare(strict_types=1);

namespace App\Services\Panel;

use App\Models\NotificacionCliente;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class NotificacionClienteService
{
    private const ROLES_PANEL_CLIENTE = [1, 4, 5];

    /**
     * @return Collection<int, NotificacionCliente>
     */
    public function listarParaUsuario(Usuario $usuario): Collection
    {
        $idUsuario = $this->resolverIdUsuarioPanelCliente($usuario);
        if ($idUsuario === null) {
            return new Collection;
        }

        return NotificacionCliente::query()
            ->where('id_usuario_destino', $idUsuario)
            ->noLeidas()
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(250)
            ->get();
    }

    public function contarNoLeidas(Usuario $usuario): int
    {
        $idUsuario = $this->resolverIdUsuarioPanelCliente($usuario);
        if ($idUsuario === null) {
            return 0;
        }

        return NotificacionCliente::query()
            ->where('id_usuario_destino', $idUsuario)
            ->noLeidas()
            ->count();
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function marcarLeidas(Usuario $usuario, array $ids, bool $todas): int
    {
        $idUsuario = $this->resolverIdUsuarioPanelCliente($usuario);
        if ($idUsuario === null) {
            return 0;
        }

        return (int) DB::transaction(function () use ($idUsuario, $ids, $todas): int {
            if ($todas) {
                return NotificacionCliente::query()
                    ->where('id_usuario_destino', $idUsuario)
                    ->update(['leido' => 1]);
            }
            if ($ids === []) {
                return 0;
            }

            $idsLimpio = array_values(array_unique(array_map('intval', $ids)));

            return NotificacionCliente::query()
                ->where('id_usuario_destino', $idUsuario)
                ->whereIn('id', $idsLimpio)
                ->update(['leido' => 1]);
        });
    }

    public function urlDetalle(NotificacionCliente $n): string
    {
        return route('panel.cliente.solicitudes.estado', [
            'solicitud' => $n->id_solicitud,
            'nc' => (int) $n->id,
        ]);
    }

    /**
     * Al abrir “ver estado” desde la campanita: marca leída si corresponde al destinatario y a la solicitud.
     */
    public function marcarLeidaAlSeguirEnlace(Usuario $usuario, int $notificacionId, int $solicitudIdEsperada): void
    {
        if ($notificacionId <= 0) {
            return;
        }

        $idUsuario = $this->resolverIdUsuarioPanelCliente($usuario);
        if ($idUsuario === null) {
            return;
        }

        $notif = NotificacionCliente::query()
            ->where('id_usuario_destino', $idUsuario)
            ->whereKey($notificacionId)
            ->first();

        if ($notif === null) {
            return;
        }

        if ((int) $notif->id_solicitud !== $solicitudIdEsperada) {
            return;
        }

        if (! $notif->leido) {
            NotificacionCliente::query()->whereKey($notificacionId)->update(['leido' => 1]);
        }
    }

    private function resolverIdUsuarioPanelCliente(Usuario $usuario): ?int
    {
        if (! in_array((int) $usuario->id_rol, self::ROLES_PANEL_CLIENTE, true)) {
            return null;
        }

        return (int) $usuario->id_usuario;
    }
}
