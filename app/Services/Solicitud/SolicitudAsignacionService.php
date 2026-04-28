<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Domain\Enums\HistorialRespuestaCanal;
use App\Models\NotificacionProveedor;
use App\Models\Proveedor;
use App\Models\RespuestaSolicitud;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

/**
 * Transaccional con el flujo de `procesos/reportesAdmin/asignarSolicitud.php` (Laravel: sin PHPMailer aquí; correos vía notificaciones en siguiente iteración).
 */
final class SolicitudAsignacionService
{
    public function __construct(
        private readonly SolicitudNotificacionService $notificacionesSolicitud,
    ) {}

    public function asignar(
        Solicitud $solicitud,
        int $idProveedor,
        ?string $clienteFinal,
        ?string $tipoCliente,
        int $idUsuarioActor,
    ): void {
        $solicitud->loadMissing(['servicio', 'creador.cliente', 'creador']);

        $idSolicitud = (int) $solicitud->id;
        $tipoNombre = $solicitud->servicio?->nombre ?? 'solicitud';

        $proveedor = Proveedor::query()->whereKey($idProveedor)->firstOrFail();
        $comercial = trim((string) ($proveedor->nombre_comercial ?? ''));
        $nombreProveedor = $comercial !== '' ? $comercial : (string) $proveedor->razon_social_proveedor;

        $notificacionesSolicitud = $this->notificacionesSolicitud;

        DB::transaction(function () use (
            $solicitud,
            $notificacionesSolicitud,
            $idProveedor,
            $clienteFinal,
            $tipoCliente,
            $idSolicitud,
            $tipoNombre,
            $idUsuarioActor,
            $nombreProveedor,
        ): void {
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

            RespuestaSolicitud::query()->create([
                'solicitud_id' => $idSolicitud,
                'usuario_id' => $idUsuarioActor,
                'respuesta' => 'En proceso',
                'estado_anterior' => 'Registrado',
                'estado_actual' => 'En proceso',
                'fecha_respuesta' => now(),
                'canal' => HistorialRespuestaCanal::SjProveedor->value,
            ]);

            $mensajeCli = "Su solicitud #$idSolicitud de $tipoNombre ha recibido una nueva respuesta. Nuevo estado: En proceso.";
            if (str_contains($mensajeCli, (string) $idSolicitud) === false) {
                $mensajeCli .= " (Solicitud #$idSolicitud)";
            }

            $notificacionesSolicitud->mensajeParaOrganizacionCliente($solicitud, $mensajeCli);

            $mensajeProv = "Se le ha asignado la solicitud #$idSolicitud de $tipoNombre.";
            if ($clienteFinal !== null && $clienteFinal !== '') {
                $mensajeProv .= ' Cliente: '.$clienteFinal.'.';
            }
            if ($tipoCliente !== null && $tipoCliente !== '') {
                $mensajeProv .= ' TIPO: '.$tipoCliente.'.';
            }
            $mensajeProv .= ' Por favor ingrese a la plataforma para gestionarla.';

            $usuariosProveedor = Usuario::query()
                ->where('id_proveedor', $idProveedor)
                ->where('activo', 1)
                ->get();

            foreach ($usuariosProveedor as $u) {
                NotificacionProveedor::query()->create([
                    'tipo' => $tipoNombre,
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
    }
}
