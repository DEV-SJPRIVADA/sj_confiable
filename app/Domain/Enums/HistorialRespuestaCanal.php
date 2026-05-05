<?php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Audiencia permitida por fila del historial (respuesta_solicitudes.canal).
 * Operación SJ↔Cliente distinta de operación SJ↔proveedor para no revelar canal operativo al cliente.
 */
enum HistorialRespuestaCanal: string
{
    /** Cliente y usuarios SJ (roles 2, 3): comunicación frente al cliente final. */
    case ClienteSj = 'cliente_sj';

    /** Proveedor y usuarios SJ: trámite operativo con el asociado (el cliente NO debe ver estas filas). */
    case SjProveedor = 'sj_proveedor';

    /** Sólo auditoría SJ: trámite interno sin aviso ni historial visible en panel cliente. */
    case SoloSj = 'solo_sj';
}
