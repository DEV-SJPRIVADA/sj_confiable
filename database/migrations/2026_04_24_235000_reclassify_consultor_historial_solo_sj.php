<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Notas SJ de trámite hacia asociado que quedaron con canal cliente_sj no deben verse en panel cliente.
 * Heurística conservadora: mismo rol consultor (2/3), estado sin cambio en «En proceso» y texto que menciona proveedor/asociado.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE respuesta_solicitudes rs
            INNER JOIN t_usuarios u ON u.id_usuario = rs.usuario_id AND u.id_rol IN (2, 3)
            SET rs.canal = 'solo_sj'
            WHERE rs.canal = 'cliente_sj'
              AND rs.estado_anterior = rs.estado_actual
              AND rs.estado_actual = 'En proceso'
              AND (
                  LOWER(rs.respuesta) LIKE '%proveedor%'
                  OR LOWER(rs.respuesta) LIKE '%asociado%'
              )
        ");
    }

    public function down(): void
    {
        // No se revierte: no hay criterio seguro para distinguir filas tocadas por esta migración.
    }
};
