<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Domain\Enums\HistorialRespuestaCanal;
use App\Models\Documento;
use App\Models\DocumentoRespuesta;
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
     * @param  list<string>  $refsAdjuntosNotificacion  Referencias `doc-{id}` y `dresp-{id}` mencionadas en el aviso al cliente
     */
    public function registrarRespuestaSj(
        Solicitud $solicitud,
        Usuario $actorConsultor,
        string $texto,
        string $nuevoEstado,
        array $archivosPdf,
        array $refsAdjuntosNotificacion = [],
        bool $visibleParaOrganizacionCliente = true,
    ): void {
        $canalHistorial = $visibleParaOrganizacionCliente
            ? HistorialRespuestaCanal::ClienteSj
            : HistorialRespuestaCanal::SoloSj;

        DB::transaction(function () use (
            $solicitud,
            $actorConsultor,
            $texto,
            $nuevoEstado,
            $archivosPdf,
            $canalHistorial,
            $visibleParaOrganizacionCliente,
            $refsAdjuntosNotificacion,
        ): void {
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
                'canal' => $canalHistorial->value,
            ]);

            $idsDocumentosEsteEnvio = [];
            $i = 0;
            foreach ($archivosPdf as $f) {
                if ($i >= 10) {
                    break;
                }
                if ($f instanceof UploadedFile && $f->isValid()) {
                    $creado = $this->adjuntarCliente->adjuntar(
                        $solicitud,
                        $f,
                        $visibleParaOrganizacionCliente,
                        false,
                    );
                    $idsDocumentosEsteEnvio[] = (int) $creado->id;
                    $i++;
                }
            }

            if ($visibleParaOrganizacionCliente) {
                self::sincronizarVisibilidadAdjuntosParaCliente(
                    (int) $solicitud->id,
                    $refsAdjuntosNotificacion,
                    $idsDocumentosEsteEnvio,
                );
            }
        });

        $solicitud->refresh()->loadMissing(['creador.cliente', 'serviciosPivote', 'servicio', 'paquete']);

        if (! $visibleParaOrganizacionCliente) {
            return;
        }

        $idSol = (int) $solicitud->id;
        $tipo = mb_substr((string) $solicitud->labelServiciosContratados(), 0, 120);
        $nombresAdjuntosCliente = self::nombresAdjuntosParaAvisoCliente($solicitud, $refsAdjuntosNotificacion, $archivosPdf);
        $listaAdj = '';
        if ($nombresAdjuntosCliente !== []) {
            $listaAdj = ' Adjuntos en este aviso: '.implode(', ', $nombresAdjuntosCliente).'.';
        }
        $msg = sprintf(
            'Su solicitud #%d (%s) tiene una nueva respuesta del equipo SJ. Estado: %s.%s',
            $idSol,
            $tipo !== '' ? $tipo : 'solicitud',
            self::etiquetaEstadoVisible($nuevoEstado),
            $listaAdj,
        );

        $this->notificacionesSolicitud->mensajeParaOrganizacionCliente($solicitud, $msg);
    }

    /**
     * @param  list<string>  $refsAdjuntosNotificacion
     * @param  list<UploadedFile>|array<int, mixed>  $archivosPdf
     * @return list<string>
     */
    private static function nombresAdjuntosParaAvisoCliente(Solicitud $solicitud, array $refsAdjuntosNotificacion, array $archivosPdf): array
    {
        $out = [];
        $idSol = (int) $solicitud->id;
        foreach ($refsAdjuntosNotificacion as $ref) {
            if (! is_string($ref)) {
                continue;
            }
            if (preg_match('/^doc-(\d+)$/', $ref, $m)) {
                $nom = Documento::query()
                    ->where('solicitud_id', $idSol)
                    ->whereKey((int) $m[1])
                    ->value('nombre_documento');
                if (is_string($nom) && $nom !== '') {
                    $out[] = $nom;
                }
            } elseif (preg_match('/^dresp-(\d+)$/', $ref, $m)) {
                $nom = DocumentoRespuesta::query()
                    ->whereKey((int) $m[1])
                    ->whereHas('respuestaMadre', fn ($q) => $q->where('solicitud_id', $idSol))
                    ->value('nombre_documentoResp');
                if (is_string($nom) && $nom !== '') {
                    $out[] = $nom;
                }
            }
        }
        foreach ($archivosPdf as $f) {
            if ($f instanceof UploadedFile && $f->isValid()) {
                $name = $f->getClientOriginalName();
                if ($name !== '') {
                    $out[] = $name;
                }
            }
        }

        return array_values(array_unique($out));
    }

    /**
     * Alinea visibilidad en panel/descarga del cliente con los adjuntos marcados en el envío «al cliente».
     *
     * @param  list<string>  $refsAdjuntosNotificacion
     * @param  list<int>  $idsDocumentosAdjuntadosEnEsteEnvio
     */
    private static function sincronizarVisibilidadAdjuntosParaCliente(
        int $solicitudId,
        array $refsAdjuntosNotificacion,
        array $idsDocumentosAdjuntadosEnEsteEnvio,
    ): void {
        $idsDocChecked = [];
        $idsDrespChecked = [];
        foreach ($refsAdjuntosNotificacion as $ref) {
            if (! is_string($ref)) {
                continue;
            }
            if (preg_match('/^doc-(\d+)$/', $ref, $m) === 1) {
                $idsDocChecked[] = (int) $m[1];
            } elseif (preg_match('/^dresp-(\d+)$/', $ref, $m) === 1) {
                $idsDrespChecked[] = (int) $m[1];
            }
        }
        $idsDocChecked = array_values(array_unique($idsDocChecked));
        $idsDrespChecked = array_values(array_unique($idsDrespChecked));

        DocumentoRespuesta::query()
            ->whereHas('respuestaMadre', fn ($q) => $q->where('solicitud_id', $solicitudId))
            ->update(['visible_para_cliente' => false]);

        if ($idsDrespChecked !== []) {
            DocumentoRespuesta::query()
                ->whereIn('id', $idsDrespChecked)
                ->whereHas('respuestaMadre', fn ($q) => $q->where('solicitud_id', $solicitudId))
                ->update(['visible_para_cliente' => true]);
        }

        Documento::query()
            ->where('solicitud_id', $solicitudId)
            ->where(function ($q): void {
                $q->where('cargado_desde_panel_cliente', false)
                    ->orWhereNull('cargado_desde_panel_cliente');
            })
            ->update(['visible_para_cliente' => false]);

        Documento::query()
            ->where('solicitud_id', $solicitudId)
            ->where('cargado_desde_panel_cliente', true)
            ->update(['visible_para_cliente' => true]);

        if ($idsDocChecked !== []) {
            Documento::query()
                ->where('solicitud_id', $solicitudId)
                ->whereIn('id', $idsDocChecked)
                ->update(['visible_para_cliente' => true]);
        }

        if ($idsDocumentosAdjuntadosEnEsteEnvio !== []) {
            Documento::query()
                ->where('solicitud_id', $solicitudId)
                ->whereIn('id', $idsDocumentosAdjuntadosEnEsteEnvio)
                ->update(['visible_para_cliente' => true]);
        }
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
