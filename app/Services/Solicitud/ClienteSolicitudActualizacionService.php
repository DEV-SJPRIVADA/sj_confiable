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

final class ClienteSolicitudActualizacionService
{
    public function __construct(
        private readonly SolicitudNotificacionService $notificaciones,
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
     *   servicio_ids: list<int>
     * }  $data
     */
    public function actualizar(Usuario $actor, Solicitud $solicitud, array $data): Solicitud
    {
        $cliente = Cliente::query()->findOrFail((int) $actor->id_cliente);
        $estadoPrevio = trim((string) ($solicitud->estado ?? ''));

        $solicitud = DB::transaction(function () use ($cliente, $solicitud, $data, $actor, $estadoPrevio): Solicitud {
            $servicioIds = array_values(array_unique(array_map('intval', $data['servicio_ids'] ?? [])));
            if ($servicioIds === [] || count($servicioIds) > 5) {
                throw new \InvalidArgumentException('Debe seleccionar entre 1 y 5 servicios.');
            }

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
            $solicitud->comentarios = isset($data['comentarios']) && trim((string) $data['comentarios']) !== ''
                ? (string) $data['comentarios'] : null;

            // Paridad legado: actualizarSolicitud solo toca servicio_id; paquete_id no se modifica en edición.
            $solicitud->servicio_id = $servicioIds[0];

            $solicitud->save();

            DB::table('solicitud_servicios')->where('solicitud_id', (int) $solicitud->id)->delete();
            foreach ($servicioIds as $sid) {
                DB::table('solicitud_servicios')->insert([
                    'solicitud_id' => (int) $solicitud->id,
                    'servicio_id' => $sid,
                ]);
            }

            $estadoActual = trim((string) ($solicitud->estado ?? ''));
            $login = trim((string) ($actor->usuario ?? ''));
            $textoHistorial = 'La organización cliente modificó los datos de la solicitud.';
            if ($login !== '') {
                $textoHistorial .= ' Usuario: '.$login.'.';
            }
            $comCliente = trim((string) ($solicitud->comentarios ?? ''));
            if ($comCliente !== '') {
                $textoHistorial .= "\n\nComentario:\n".$comCliente;
            }

            RespuestaSolicitud::query()->create(
                RespuestaSolicitudHistorial::atributos([
                    'solicitud_id' => (int) $solicitud->id,
                    'usuario_id' => (int) $actor->id_usuario,
                    'respuesta' => $textoHistorial,
                    'estado_anterior' => $estadoPrevio !== '' ? $estadoPrevio : null,
                    'estado_actual' => $estadoActual !== '' ? $estadoActual : $estadoPrevio,
                    'fecha_respuesta' => now(),
                ], HistorialRespuestaCanal::ClienteSj),
            );

            return $solicitud->fresh(['serviciosPivote', 'paquete']);
        });

        $idSol = (int) $solicitud->id;
        $actorId = (int) $actor->id_usuario;
        $notificaciones = $this->notificaciones;
        app()->terminating(static function () use ($notificaciones, $idSol, $actorId): void {
            try {
                $fresh = Solicitud::query()->find($idSol);
                $actorFresh = Usuario::query()->find($actorId);
                if ($fresh !== null && $actorFresh !== null) {
                    $notificaciones->solicitudEditadaPorCliente($fresh, $actorFresh);
                }
            } catch (\Throwable $e) {
                Log::error('No se pudo notificar edición de solicitud a consultores SJ.', [
                    'solicitud_id' => $idSol,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        return $solicitud;
    }
}
