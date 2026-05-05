<?php

declare(strict_types=1);

use App\Domain\Enums\HistorialRespuestaCanal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Incorpora solicitudes.comentarios en la primera fila de historial (alta cliente) si aún no estaba reflejada.
 */
return new class extends Migration
{
    private const TEXTOS_SIN_COMENTARIO = [
        'Solicitud registrada desde el panel cliente.',
        'Solicitud registrada desde el panel cliente (historial retroactivo).',
    ];

    public function up(): void
    {
        $rows = DB::table('respuesta_solicitudes as rs')
            ->join('solicitudes as s', 's.id', '=', 'rs.solicitud_id')
            ->where('rs.canal', HistorialRespuestaCanal::ClienteSj->value)
            ->whereNull('rs.estado_anterior')
            ->where('rs.estado_actual', 'Registrado')
            ->whereNotNull('s.comentarios')
            ->where('s.comentarios', '!=', '')
            ->select('rs.id', 'rs.respuesta', 's.comentarios')
            ->get();

        foreach ($rows as $r) {
            $base = trim((string) $r->respuesta);
            if (! in_array($base, self::TEXTOS_SIN_COMENTARIO, true)) {
                continue;
            }
            if (str_contains($base, 'Comentario:')) {
                continue;
            }
            $com = trim((string) $r->comentarios);
            if ($com === '') {
                continue;
            }
            DB::table('respuesta_solicitudes')->where('id', (int) $r->id)->update([
                'respuesta' => $base."\n\nComentario:\n".$com,
            ]);
        }
    }

    public function down(): void
    {
        // Sin reversión fiable (mezclaría comentarios editados a mano en historial).
    }
};
