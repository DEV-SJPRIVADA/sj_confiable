<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Domain\Enums\HistorialRespuestaCanal;
use App\Models\NotificacionProveedor;
use App\Models\Proveedor;
use App\Models\RespuestaSolicitud;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Support\RespuestaSolicitudHistorial;
use Illuminate\Support\Facades\DB;

/**
 * Transaccional con el flujo de `procesos/reportesAdmin/asignarSolicitud.php`.
 * Avisos en panel y correo al proveedor; sin aviso al cliente (canal operativo SJ↔proveedor).
 *
 * La asignación al asociado no genera avisos en `notificaciones_cliente`: el cliente no participa del canal operativo SJ↔proveedor.
 */
final class SolicitudAsignacionService
{
    public function __construct(
        private readonly SolicitudCorreoNotificacionService $correos,
    ) {}

    public function asignar(
        Solicitud $solicitud,
        int $idProveedor,
        ?string $clienteFinal,
        ?string $tipoCliente,
        int $idUsuarioActor,
        ?string $comentarioAsignacion = null,
    ): void {
        $solicitud->loadMissing(['servicio', 'creador.cliente', 'creador']);

        $idSolicitud = (int) $solicitud->id;
        $tipoNombre = $solicitud->servicio?->nombre ?? 'solicitud';

        $proveedor = Proveedor::query()->whereKey($idProveedor)->firstOrFail();
        $comercial = trim((string) ($proveedor->nombre_comercial ?? ''));
        $nombreProveedor = $comercial !== '' ? $comercial : (string) $proveedor->razon_social_proveedor;

        $mensajeProv = "Se le ha asignado la solicitud #$idSolicitud de $tipoNombre.";
        if ($clienteFinal !== null && $clienteFinal !== '') {
            $mensajeProv .= ' Cliente: '.$clienteFinal.'.';
        }
        if ($tipoCliente !== null && $tipoCliente !== '') {
            $mensajeProv .= ' TIPO: '.$tipoCliente.'.';
        }
        $mensajeProv .= ' Por favor ingrese a la plataforma para gestionarla.';

        $comentario = trim((string) ($comentarioAsignacion ?? ''));

        $textoHistorialAsignacion = sprintf(
            'Solicitud asignada al asociado de negocio %s.',
            $nombreProveedor,
        );
        if ($comentario !== '') {
            $textoHistorialAsignacion .= "\n\nComentario:\n".$comentario;
            $mensajeProv .= "\n\nComentario del consultor:\n".$comentario;
        }

        DB::transaction(function () use (
            $solicitud,
            $idProveedor,
            $clienteFinal,
            $tipoCliente,
            $idSolicitud,
            $tipoNombre,
            $idUsuarioActor,
            $nombreProveedor,
            $mensajeProv,
            $textoHistorialAsignacion,
        ): void {
            $estadoPrevio = trim((string) ($solicitud->estado ?? ''));

            $solicitud->estado = 'En proceso';
            $solicitud->id_proveedor = $idProveedor;
            $solicitud->fecha_asignacion_proveedor = now();

            if ($clienteFinal !== null && $clienteFinal !== '') {
                $solicitud->cliente_final = $clienteFinal;
            }
            if ($tipoCliente !== null && $tipoCliente !== '') {
                $solicitud->tipo_cliente = $tipoCliente;
            }

            $solicitud->save();

            RespuestaSolicitud::query()->create(
                RespuestaSolicitudHistorial::atributos([
                    'solicitud_id' => $idSolicitud,
                    'usuario_id' => $idUsuarioActor,
                    'respuesta' => $textoHistorialAsignacion,
                    'estado_anterior' => $estadoPrevio !== '' ? $estadoPrevio : null,
                    'estado_actual' => 'En proceso',
                    'fecha_respuesta' => now(),
                ], HistorialRespuestaCanal::SjProveedor),
            );

            $usuariosProveedor = Usuario::query()
                ->where('id_proveedor', $idProveedor)
                ->where('activo', 1)
                ->get();

            $tipoNotif = mb_substr($tipoNombre, 0, 30);

            foreach ($usuariosProveedor as $u) {
                NotificacionProveedor::query()->create([
                    'tipo' => $tipoNotif,
                    'proveedor_nombre' => $nombreProveedor,
                    'id_solicitud' => $idSolicitud,
                    'mensaje' => $mensajeProv,
                    'id_proveedor_destino' => $idProveedor,
                    'id_usuario_destino' => (int) $u->id_usuario,
                    'leido' => 0,
                    'fecha' => now(),
                ]);
            }
        });

        $solicitud->refresh();
        $nombreClienteFinal = $this->correos->nombreClienteFinalParaAviso($solicitud);

        // Correo tras enviar la respuesta HTTP (evita 500 por timeout SMTP antes del redirect).
        $correos = $this->correos;
        app()->terminating(static function () use (
            $correos,
            $idProveedor,
            $tipoNombre,
            $nombreClienteFinal,
            $idSolicitud,
            $mensajeProv,
        ): void {
            $correos->asignacionParaProveedor(
                $idProveedor,
                $tipoNombre,
                $nombreClienteFinal,
                $idSolicitud,
                $mensajeProv,
            );
        });
    }
}
