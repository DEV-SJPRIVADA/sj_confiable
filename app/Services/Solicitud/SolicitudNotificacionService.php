<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Models\Notificacion;
use App\Models\NotificacionCliente;
use App\Models\NotificacionProveedor;
use App\Models\Proveedor;
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

    public function __construct(
        private readonly SolicitudCorreoNotificacionService $correos,
    ) {}

    /**
     * Cliente registra una nueva solicitud: avisa solo a SJ (consultores roles 2 y 3).
     * No se notifica a la organización cliente (quien creó el registro ya lo conoce).
     */
    public function nuevaSolicitudDesdeCliente(Solicitud $solicitud): void
    {
        $solicitud->loadMissing(['creador.cliente', 'creador.persona', 'serviciosPivote', 'servicio', 'paquete']);
        $creador = $solicitud->creador;
        if ($creador === null || $creador->id_cliente === null) {
            return;
        }

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

        $this->correos->nuevaSolicitudParaConsultoresSj(
            $tipo,
            $razonCliente,
            $idSol,
            $mensajeConsultor,
            $this->correos->lineaCreadorSolicitud($solicitud),
        );
    }

    /**
     * La organización cliente modifica datos de la solicitud: aviso a consultores SJ.
     */
    public function solicitudEditadaPorCliente(Solicitud $solicitud, Usuario $actor): void
    {
        $solicitud->loadMissing(['creador.cliente', 'creador.persona', 'serviciosPivote', 'servicio', 'paquete']);
        $razonCliente = (string) ($solicitud->creador?->cliente?->razon_social ?? '—');
        $nombreClienteFinal = $this->correos->nombreClienteFinalParaAviso($solicitud);
        $tipo = $this->truncarTipo((string) $solicitud->labelServiciosContratados());
        $idSol = (int) $solicitud->id;
        $login = trim((string) ($actor->usuario ?? ''));

        $mensajeConsultor = sprintf(
            'El cliente %s modificó la solicitud #%d (%s). Usuario: %s.',
            $razonCliente,
            $idSol,
            $tipo,
            $login !== '' ? $login : '—',
        );

        $this->broadcastConsultoresSj($tipo, $razonCliente, $idSol, $mensajeConsultor);

        $this->correos->edicionParaConsultoresSj(
            $tipo,
            $nombreClienteFinal,
            $idSol,
            $mensajeConsultor,
            $this->correos->lineaCreadorSolicitud($solicitud),
            $razonCliente,
        );
    }

    /**
     * La organización cliente cancela / inactiva la solicitud: aviso a consultores SJ.
     */
    public function solicitudCanceladaPorCliente(Solicitud $solicitud, Usuario $actor): void
    {
        $solicitud->loadMissing(['creador.cliente', 'creador.persona', 'serviciosPivote', 'servicio', 'paquete', 'proveedorAsignado']);
        $razonCliente = (string) ($solicitud->creador?->cliente?->razon_social ?? '—');
        $nombreClienteFinal = $this->correos->nombreClienteFinalParaAviso($solicitud);
        $tipo = $this->truncarTipo((string) $solicitud->labelServiciosContratados());
        $idSol = (int) $solicitud->id;
        $login = trim((string) ($actor->usuario ?? ''));

        $mensajeConsultor = sprintf(
            'El cliente %s canceló la solicitud #%d (%s). Usuario: %s.',
            $razonCliente,
            $idSol,
            $tipo,
            $login !== '' ? $login : '—',
        );

        $this->broadcastConsultoresSj($tipo, $razonCliente, $idSol, $mensajeConsultor);

        $this->correos->cancelacionParaConsultoresSj(
            $tipo,
            $nombreClienteFinal,
            $idSol,
            $mensajeConsultor,
            $this->correos->lineaCreadorSolicitud($solicitud),
            $razonCliente,
        );

        $idProveedor = (int) ($solicitud->id_proveedor ?? 0);
        if ($idProveedor <= 0) {
            return;
        }

        $proveedor = $solicitud->proveedorAsignado ?? Proveedor::query()->whereKey($idProveedor)->first();
        $nombreProveedor = $this->nombreComercialProveedor($proveedor);
        $mensajeProveedor = sprintf(
            'La organización cliente canceló la solicitud #%d (%s). Cliente final: %s.',
            $idSol,
            $tipo,
            $nombreClienteFinal,
        );

        $usuariosProveedor = Usuario::query()
            ->where('id_proveedor', $idProveedor)
            ->where('activo', 1)
            ->get();

        foreach ($usuariosProveedor as $u) {
            NotificacionProveedor::query()->create([
                'tipo' => $tipo,
                'proveedor_nombre' => $nombreProveedor,
                'id_solicitud' => $idSol,
                'mensaje' => mb_substr($mensajeProveedor, 0, 255),
                'id_proveedor_destino' => $idProveedor,
                'id_usuario_destino' => (int) $u->id_usuario,
                'leido' => 0,
                'fecha' => now(),
            ]);
        }

        $this->correos->cancelacionParaProveedor(
            $idProveedor,
            $tipo,
            $nombreClienteFinal,
            $idSol,
            $mensajeProveedor,
        );
    }

    /**
     * El asociado u operación devuelve información relevante a SJ (p. ej. documento listo): notificar consultores.
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

        $this->correos->respuestaProveedorParaConsultoresSj(
            $tipo,
            $razon,
            (int) $solicitud->id,
            $contenido,
        );
    }

    /**
     * Notificación a la organización cliente ante un hito (respuesta SJ visible al cliente).
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

        $this->correos->mensajeParaOrganizacionCliente(
            $idCliente,
            $tipo,
            $razon,
            $idSol,
            $mensaje,
        );
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

    private function nombreComercialProveedor(?Proveedor $proveedor): string
    {
        if ($proveedor === null) {
            return '—';
        }

        $comercial = trim((string) ($proveedor->nombre_comercial ?? ''));
        if ($comercial !== '') {
            return $comercial;
        }

        $razon = trim((string) ($proveedor->razon_social_proveedor ?? ''));

        return $razon !== '' ? $razon : '—';
    }
}
