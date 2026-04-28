<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Domain\Enums\HistorialRespuestaCanal;
use App\Models\RespuestaSolicitud;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final class ConsultorSolicitudRespuestaService
{
    public function __construct(
        private readonly ClienteSolicitudDocumentoAdjuntoService $adjuntarCliente,
        private readonly SolicitudNotificacionService $notificacionesSolicitud,
    ) {}

    /**
     * @param  list<UploadedFile>|array<int, mixed>  $archivosPdf
     */
    public function registrarRespuestaSj(
        Solicitud $solicitud,
        Usuario $actorConsultor,
        string $texto,
        string $nuevoEstado,
        array $archivosPdf,
    ): void {
        DB::transaction(function () use ($solicitud, $actorConsultor, $texto, $nuevoEstado, $archivosPdf): void {
            $previo = trim((string) ($solicitud->estado ?? ''));
            $solicitud->estado = $nuevoEstado;
            $solicitud->save();

            RespuestaSolicitud::query()->create([
                'solicitud_id' => (int) $solicitud->id,
                'usuario_id' => (int) $actorConsultor->id_usuario,
                'respuesta' => trim($texto),
                'estado_anterior' => $previo !== '' ? $previo : null,
                'estado_actual' => $nuevoEstado,
                'fecha_respuesta' => now(),
                'canal' => HistorialRespuestaCanal::ClienteSj->value,
            ]);

            $i = 0;
            foreach ($archivosPdf as $f) {
                if ($i >= 10) {
                    break;
                }
                if ($f instanceof UploadedFile && $f->isValid()) {
                    $this->adjuntarCliente->adjuntar($solicitud, $f);
                    $i++;
                }
            }
        });

        $solicitud->refresh()->loadMissing(['creador.cliente', 'serviciosPivote', 'servicio', 'paquete']);

        $idSol = (int) $solicitud->id;
        $tipo = mb_substr((string) $solicitud->labelServiciosContratados(), 0, 120);
        $msg = sprintf(
            'Su solicitud #%d (%s) tiene una nueva respuesta del equipo SJ. Estado: %s.',
            $idSol,
            $tipo !== '' ? $tipo : 'solicitud',
            self::etiquetaEstadoVisible($nuevoEstado),
        );

        $this->notificacionesSolicitud->mensajeParaOrganizacionCliente($solicitud, $msg);
    }

    private static function etiquetaEstadoVisible(string $estadoBd): string
    {
        return match ($estadoBd) {
            'En proceso' => 'En proceso',
            'Completado' => 'Completado',
            'Cancelado' => 'Cancelado',
            'Registrado' => 'Registrado',
            'Nuevo' => 'Nuevo',
            default => $estadoBd,
        };
    }
}
