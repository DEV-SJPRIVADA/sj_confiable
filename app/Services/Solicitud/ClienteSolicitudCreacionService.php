<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Domain\Enums\HistorialRespuestaCanal;
use App\Models\Cliente;
use App\Models\RespuestaSolicitud;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Support\RespuestaSolicitudHistorial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Registro de solicitud desde el panel cliente (criterio legado: creador = usuario de la sesión, empresa = cliente vinculado).
 */
final class ClienteSolicitudCreacionService
{
    public function __construct(
        private readonly SolicitudNotificacionService $notificacionesSolicitud,
    ) {}
    /**
     * @param  array{
     *   cliente_final: string,
     *   nombres: string,
     *   apellidos: string,
     *   cargo_candidato: string,
     *   tipo_identificacion: string,
     *   numero_documento: string,
     *   celular: string,
     *   direccion_residencia: string,
     *   ciudad_prestacion_servicio: string,
     *   ciudad_solicitud_servicio: string,
     *   ciudad_residencia_evaluado: string,
     *   comentarios?: string|null,
     *   telefono_fijo?: string|null,
     *   fecha_expedicion?: string|null,
     *   lugar_expedicion?: string|null,
     *   paquete_id?: int|null,
     *   servicio_ids?: list<int>
     * }  $data
     */
    public function crear(Usuario $actor, array $data): Solicitud
    {
        $cliente = Cliente::query()->findOrFail((int) $actor->id_cliente);

        $solicitud = DB::transaction(function () use ($actor, $cliente, $data): Solicitud {
            $paqueteId = ! empty($data['paquete_id']) ? (int) $data['paquete_id'] : null;
            $servicioIds = array_values(array_unique(array_map('intval', $data['servicio_ids'] ?? [])));

            $solicitud = new Solicitud;
            $solicitud->empresa_solicitante = (string) $cliente->razon_social;
            $solicitud->nit_empresa_solicitante = (string) $cliente->NIT;
            $solicitud->cliente_final = (string) $data['cliente_final'];
            $solicitud->ciudad_prestacion_servicio = (string) $data['ciudad_prestacion_servicio'];
            $solicitud->ciudad_solicitud_servicio = (string) $data['ciudad_solicitud_servicio'];
            $solicitud->nombres = (string) $data['nombres'];
            $solicitud->apellidos = (string) $data['apellidos'];
            $solicitud->cargo_candidato = (string) $data['cargo_candidato'];
            $solicitud->tipo_identificacion = (string) $data['tipo_identificacion'];
            $solicitud->numero_documento = (string) $data['numero_documento'];
            $solicitud->fecha_expedicion = ! empty($data['fecha_expedicion'])
                ? \Illuminate\Support\Carbon::parse($data['fecha_expedicion'])->format('Y-m-d') : null;
            $solicitud->lugar_expedicion = isset($data['lugar_expedicion']) && (string) $data['lugar_expedicion'] !== ''
                ? (string) $data['lugar_expedicion'] : null;
            $solicitud->telefono_fijo = $data['telefono_fijo'] ?? null;
            $solicitud->celular = (string) $data['celular'];
            $solicitud->ciudad_residencia_evaluado = (string) $data['ciudad_residencia_evaluado'];
            $solicitud->direccion_residencia = (string) $data['direccion_residencia'];
            $solicitud->comentarios = array_key_exists('comentarios', $data)
                ? (trim((string) $data['comentarios']) !== '' ? trim((string) $data['comentarios']) : null)
                : null;
            $solicitud->usuario_id = (int) $actor->id_usuario;
            $solicitud->estado = 'Registrado';
            $solicitud->activo = true;
            $solicitud->fecha_creacion = now();

            if ($paqueteId !== null) {
                $solicitud->paquete_id = $paqueteId;
                $solicitud->servicio_id = null;
            } else {
                $solicitud->paquete_id = null;
                $solicitud->servicio_id = $servicioIds[0] ?? null;
            }

            if ($solicitud->servicio_id === null && $solicitud->paquete_id === null) {
                throw new \InvalidArgumentException('Solicitud sin servicio o paquete.');
            }

            $solicitud->save();

            $textoHistorial = 'Solicitud registrada desde el panel cliente.';
            $comCliente = trim((string) ($solicitud->comentarios ?? ''));
            if ($comCliente !== '') {
                $textoHistorial .= "\n\nComentario:\n".$comCliente;
            }

            $ahora = now();
            RespuestaSolicitud::query()->create(
                RespuestaSolicitudHistorial::atributos([
                    'solicitud_id' => (int) $solicitud->id,
                    'usuario_id' => (int) $actor->id_usuario,
                    'respuesta' => $textoHistorial,
                    'estado_anterior' => null,
                    'estado_actual' => 'Registrado',
                    'fecha_respuesta' => $ahora,
                ], HistorialRespuestaCanal::ClienteSj),
            );

            if ($paqueteId === null && $servicioIds !== []) {
                foreach ($servicioIds as $sid) {
                    DB::table('solicitud_servicios')->insert([
                        'solicitud_id' => (int) $solicitud->id,
                        'servicio_id' => $sid,
                    ]);
                }
            }

            return $solicitud;
        });

        $idSol = (int) $solicitud->id;
        if ($idSol > 0) {
            $notificaciones = $this->notificacionesSolicitud;
            app()->terminating(static function () use ($notificaciones, $idSol): void {
                try {
                    $fresh = Solicitud::query()->find($idSol);
                    if ($fresh !== null) {
                        $notificaciones->nuevaSolicitudDesdeCliente($fresh);
                    }
                } catch (\Throwable $e) {
                    Log::error('No se pudo notificar nueva solicitud a consultores SJ.', [
                        'solicitud_id' => $idSol,
                        'error' => $e->getMessage(),
                    ]);
                }
            });
        }

        return $solicitud;
    }
}
