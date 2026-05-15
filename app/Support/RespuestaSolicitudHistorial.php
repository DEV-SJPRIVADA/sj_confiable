<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\Enums\HistorialRespuestaCanal;
use Illuminate\Support\Facades\Schema;

/**
 * Atributos de inserción en respuesta_solicitudes compatibles con BD sin migración de canal.
 */
final class RespuestaSolicitudHistorial
{
    /**
     * @param  array<string, mixed>  $atributos
     * @return array<string, mixed>
     */
    public static function atributos(array $atributos, HistorialRespuestaCanal $canal): array
    {
        if (Schema::hasColumn('respuesta_solicitudes', 'canal')) {
            $atributos['canal'] = $canal->value;
        }

        return $atributos;
    }
}
