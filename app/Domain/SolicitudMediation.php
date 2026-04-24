<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Texto de referencia del flujo: SJ Seguridad medía entre cliente y asociado de negocios.
 */
final class SolicitudMediation
{
    public const
        TEXT = 'SJ Seguridad media entre el cliente y el asociado de negocios: el cliente no contacta al '
        .'proveedor; el consultor recibe, asigna, recibe la respuesta del asociado y transmite al cliente; el cierre lo define el consultor.';

    private function __construct() {}
}
