<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Domain\Enums\HistorialRespuestaCanal;
use App\Models\RespuestaSolicitud;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Support\RespuestaSolicitudHistorial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ClienteSolicitudCancelacionService
{
    public function __construct(
        private readonly SolicitudNotificacionService $notificaciones,
    ) {}

    public function cancelar(Solicitud $solicitud, Usuario $actor): void
    {
        $previo = trim((string) ($solicitud->estado ?? ''));

        DB::transaction(function () use ($solicitud, $actor, $previo): void {
            $solicitud->estado = 'Cancelado';
            $solicitud->activo = 0;
            $solicitud->save();

            RespuestaSolicitud::query()->create(
                RespuestaSolicitudHistorial::atributos([
                    'solicitud_id' => (int) $solicitud->id,
                    'usuario_id' => (int) $actor->id_usuario,
                    'respuesta' => 'Solicitud cancelada por la organización cliente.',
                    'estado_anterior' => $previo !== '' ? $previo : null,
                    'estado_actual' => 'Cancelado',
                    'fecha_respuesta' => now(),
                ], HistorialRespuestaCanal::ClienteSj),
            );
        });

        $solicitud->refresh();

        $idSol = (int) $solicitud->id;
        $actorId = (int) $actor->id_usuario;
        $notificaciones = $this->notificaciones;
        app()->terminating(static function () use ($notificaciones, $idSol, $actorId): void {
            try {
                $fresh = Solicitud::query()->find($idSol);
                $actorFresh = Usuario::query()->find($actorId);
                if ($fresh !== null && $actorFresh !== null) {
                    $notificaciones->solicitudCanceladaPorCliente($fresh, $actorFresh);
                }
            } catch (\Throwable $e) {
                Log::error('No se pudo notificar cancelación de solicitud a consultores SJ.', [
                    'solicitud_id' => $idSol,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }
}
