<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Models\Cliente;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

final class ClienteSolicitudActualizacionService
{
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
    public function actualizar(Usuario $actor, Solicitud $solicitud, array $data): Solicitud
    {
        $cliente = Cliente::query()->findOrFail((int) $actor->id_cliente);

        return DB::transaction(function () use ($cliente, $solicitud, $data): Solicitud {
            $paqueteId = ! empty($data['paquete_id']) ? (int) $data['paquete_id'] : null;
            $servicioIds = array_values(array_unique(array_map('intval', $data['servicio_ids'] ?? [])));

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

            DB::table('solicitud_servicios')->where('solicitud_id', (int) $solicitud->id)->delete();

            if ($paqueteId === null && $servicioIds !== []) {
                foreach ($servicioIds as $sid) {
                    DB::table('solicitud_servicios')->insert([
                        'solicitud_id' => (int) $solicitud->id,
                        'servicio_id' => $sid,
                    ]);
                }
            }

            return $solicitud->fresh(['serviciosPivote', 'paquete']);
        });
    }
}
