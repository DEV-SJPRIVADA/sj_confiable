<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Domain\Enums\HistorialRespuestaCanal;
use App\Models\DocumentoRespuesta;
use App\Models\RespuestaMadre;
use App\Models\RespuestaSolicitud;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ProveedorSolicitudRespuestaService
{
    public function __construct(
        private readonly SolicitudNotificacionService $notificacionesSolicitud,
    ) {}

    /**
     * @param  list<UploadedFile>|array<int, mixed>  $archivosPdf
     */
    public function registrarRespuestaProveedor(
        Solicitud $solicitud,
        Usuario $actorProveedor,
        string $texto,
        string $nuevoEstado,
        array $archivosPdf,
    ): void {
        DB::transaction(function () use ($solicitud, $actorProveedor, $texto, $nuevoEstado, $archivosPdf): void {
            $previo = trim((string) ($solicitud->estado ?? ''));
            $solicitud->estado = $nuevoEstado;
            $solicitud->save();

            $madre = RespuestaMadre::query()->create([
                'solicitud_id' => (int) $solicitud->id,
                'usuario_id' => (int) $actorProveedor->id_usuario,
                'respuesta' => trim($texto),
                'estado_actual' => $nuevoEstado,
                'fecha_creacion' => now(),
            ]);

            $primerNombreAdjunto = null;
            $i = 0;
            foreach ($archivosPdf as $f) {
                if ($i >= 10) {
                    break;
                }
                if (! ($f instanceof UploadedFile) || ! $f->isValid()) {
                    continue;
                }

                $original = $f->getClientOriginalName();
                $stored = $f->storeAs(
                    'uploads',
                    'resp_'.Str::random(12).'_'.uniqid('', true).'.pdf',
                    'public',
                );

                DocumentoRespuesta::query()->create([
                    'respuesta_madre_id' => (int) $madre->id,
                    'nombre_documentoResp' => $original !== '' ? $original : basename($stored),
                    'ruta_documentoResp' => $stored,
                    'fecha_subidaResp' => now(),
                    'visible_para_cliente' => false,
                ]);

                if ($primerNombreAdjunto === null && $original !== '') {
                    $primerNombreAdjunto = $original;
                }
                $i++;
            }

            RespuestaSolicitud::query()->create([
                'solicitud_id' => (int) $solicitud->id,
                'usuario_id' => (int) $actorProveedor->id_usuario,
                'respuesta' => trim($texto),
                'documento_respuesta' => $primerNombreAdjunto,
                'estado_anterior' => $previo !== '' ? $previo : null,
                'estado_actual' => $nuevoEstado,
                'fecha_respuesta' => now(),
                'canal' => HistorialRespuestaCanal::SjProveedor->value,
            ]);
        });

        $solicitud->refresh()->loadMissing(['proveedorAsignado', 'creador.cliente', 'serviciosPivote', 'servicio', 'paquete']);

        $idSol = (int) $solicitud->id;
        $nombreProv = (string) ($solicitud->proveedorAsignado?->nombre_comercial
            ?? $solicitud->proveedorAsignado?->razon_social_proveedor
            ?? $actorProveedor->usuario
            ?? 'Asociado');

        $this->notificacionesSolicitud->operacionProveedorParaConsultores(
            $solicitud,
            sprintf(
                '%s registró respuesta en la solicitud #%d. Estado: %s.',
                $nombreProv,
                $idSol,
                $nuevoEstado,
            ),
        );
    }
}
