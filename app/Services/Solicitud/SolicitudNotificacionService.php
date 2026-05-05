<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Models\Notificacion;
use App\Models\NotificacionCliente;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Support\Collection;

/**
 * Reglas de disparo: consultores (tabla notificaciones por rol 2 y 3 como en legado),
 * usuarios cliente (notificaciones_cliente cuando SJ envía aviso explícito al cliente),
 * proveedor (notificaciones_proveedor) en asignación y otros hitos operativos.
 */
final class SolicitudNotificacionService
{
    private const ROLES_CONSULTOR_SJ = [2, 3];

    /**
     * Cliente registra una nueva solicitud: avisa a SJ (admin/superadmin por bandeja) y a todos los usuarios activos de la misma empresa.
     */
    public function nuevaSolicitudDesdeCliente(Solicitud $solicitud): void
    {
        $solicitud->loadMissing(['creador.cliente', 'serviciosPivote', 'servicio', 'paquete']);
        $creador = $solicitud->creador;
        if ($creador === null || $creador->id_cliente === null) {
            return;
        }

        $idCliente = (int) $creador->id_cliente;
        $razonCliente = (string) ($creador->cliente?->razon_social ?? '—');
        $tipo = $this->truncarTipo((string) $solicitud->labelServiciosContratados());
        $idSol = (int) $solicitud->id;

        $mensajeConsultor = sprintf(
            'El cliente %s ha subido la solicitud #%d de %s. Dar clic para más detalle.',
            $razonCliente,
            $idSol,
            $tipo
        );

        $this->broadcastConsultoresSj($tipo, $razonCliente, $idSol, $mensajeConsultor);

        $mensajeEmpresa = sprintf(
            'Se ha registrado una nueva solicitud #%d (%s).',
            $idSol,
            $tipo
        );

        $this->broadcastUsuariosClienteOrganizacion($idCliente, $tipo, $razonCliente, $idSol, $mensajeEmpresa);
    }

    /**
     * El asociado u operación devuelve información relevante a SJ (p. ej. documento listo): notificar consultores.
     * Llamar cuando exista flujo de respuesta del proveedor registrado en app.
     */
    public function operacionProveedorParaConsultores(Solicitud $solicitud, string $mensaje): void
    {
        $solicitud->loadMissing(['creador.cliente', 'serviciosPivote', 'servicio', 'paquete']);
        $razon = (string) ($solicitud->creador?->cliente?->razon_social ?? '—');
        $tipo = $this->truncarTipo((string) $solicitud->labelServiciosContratados());

        $contenido = mb_substr(trim($mensaje), 0, 255);
        if ($contenido === '') {
            $contenido = sprintf('Actualización solicitud #%d.', (int) $solicitud->id);
        }

        $this->broadcastConsultoresSj($tipo, $razon, (int) $solicitud->id, $contenido);
    }

    /**
     * Notificación a la organización cliente ante un hito (ej. asignación a asociado — mensaje frente al cliente).
     *
     * @param  Collection<int, Usuario>|null  $soloEstos  si se pasa, sólo se notifica a estos (mismo id_cliente)
     */
    public function mensajeParaOrganizacionCliente(
        Solicitud $solicitud,
        string $mensaje,
        ?Collection $soloEstos = null,
    ): void {
        $solicitud->loadMissing(['creador.cliente', 'serviciosPivote', 'servicio', 'paquete']);
        $creador = $solicitud->creador;
        if ($creador === null || $creador->id_cliente === null) {
            return;
        }

        $idCliente = (int) $creador->id_cliente;
        $razon = (string) ($creador->cliente?->razon_social ?? '—');
        $tipo = $this->truncarTipo((string) $solicitud->labelServiciosContratados());
        $idSol = (int) $solicitud->id;

        $mensajeCorto = mb_substr($mensaje, 0, 255);

        $query = Usuario::query()
            ->where('id_cliente', $idCliente)
            ->where('activo', 1);

        if ($soloEstos !== null && $soloEstos->isNotEmpty()) {
            $ids = $soloEstos->pluck('id_usuario')->map(fn ($id) => (int) $id)->all();
            $query->whereIn('id_usuario', $ids);
        }

        foreach ($query->cursor() as $u) {
            NotificacionCliente::query()->create([
                'tipo' => $tipo,
                'cliente_nombre' => $razon,
                'id_solicitud' => $idSol,
                'mensaje' => $mensajeCorto,
                'id_usuario_destino' => (int) $u->id_usuario,
                'leido' => 0,
                'fecha' => now(),
            ]);
        }
    }

    private function broadcastUsuariosClienteOrganizacion(
        int $idCliente,
        string $tipo,
        string $razonCliente,
        int $idSolicitud,
        string $mensaje,
    ): void {
        $usuarios = Usuario::query()
            ->where('id_cliente', $idCliente)
            ->where('activo', 1)
            ->get();

        foreach ($usuarios as $u) {
            NotificacionCliente::query()->create([
                'tipo' => $tipo,
                'cliente_nombre' => $razonCliente,
                'id_solicitud' => $idSolicitud,
                'mensaje' => mb_substr($mensaje, 0, 255),
                'id_usuario_destino' => (int) $u->id_usuario,
                'leido' => 0,
                'fecha' => now(),
            ]);
        }
    }

    private function broadcastConsultoresSj(string $tipo, string $razonCliente, int $idSolicitud, string $mensaje): void
    {
        $mensaje255 = mb_substr(trim($mensaje), 0, 255);

        foreach (self::ROLES_CONSULTOR_SJ as $rolDestino) {
            Notificacion::query()->create([
                'tipo' => mb_substr($tipo, 0, 30),
                'cliente_nombre' => mb_substr($razonCliente, 0, 100),
                'id_solicitud' => $idSolicitud,
                'mensaje' => $mensaje255,
                'rol_destino' => $rolDestino,
                'leido' => 0,
                'fecha' => now(),
            ]);
        }
    }

    private function truncarTipo(string $raw): string
    {
        $t = trim($raw);

        return $t === '' ? 'solicitud' : mb_substr($t, 0, 30);
    }
}
