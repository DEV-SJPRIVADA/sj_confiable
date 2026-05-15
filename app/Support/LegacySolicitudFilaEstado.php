<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Clases de fila por estado (paridad con sj_confiable1).
 */
final class LegacySolicitudFilaEstado
{
    public static function displayEstado(?string $estado): string
    {
        $e = trim((string) $estado);
        if (strcasecmp($e, 'Registrado') === 0) {
            return 'Nuevo';
        }

        return $e;
    }

    /**
     * @return 'fila-nuevo'|'fila-en-proceso'|'fila-completado'|'fila-cancelado'|''
     */
    public static function claseCss(?string $estado): string
    {
        return match (self::displayEstado($estado)) {
            'En proceso' => 'fila-en-proceso',
            'Nuevo' => 'fila-nuevo',
            'Completado' => 'fila-completado',
            'Cancelado' => 'fila-cancelado',
            default => '',
        };
    }
}
