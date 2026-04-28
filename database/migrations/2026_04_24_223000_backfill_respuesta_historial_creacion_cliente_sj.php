<?php

declare(strict_types=1);

use App\Domain\Enums\HistorialRespuestaCanal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('respuesta_solicitudes', 'canal')) {
            return;
        }

        $canalCliente = HistorialRespuestaCanal::ClienteSj->value;

        $filas = DB::table('solicitudes as s')
            ->whereRaw(
                'NOT EXISTS (SELECT 1 FROM respuesta_solicitudes r WHERE r.solicitud_id = s.id AND r.canal = ?)',
                [$canalCliente]
            )
            ->select('s.id', 's.usuario_id', 's.fecha_creacion')
            ->get();

        foreach ($filas as $s) {
            $fechaCreacion = $s->fecha_creacion ?? null;

            DB::table('respuesta_solicitudes')->insert([
                'solicitud_id' => (int) $s->id,
                'usuario_id' => (int) $s->usuario_id,
                'respuesta' => 'Solicitud registrada desde el panel cliente (historial retroactivo).',
                'documento_respuesta' => null,
                'fecha_respuesta' => $fechaCreacion !== null ? Carbon::parse($fechaCreacion)->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                'estado_anterior' => null,
                'estado_actual' => 'Registrado',
                'canal' => $canalCliente,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('respuesta_solicitudes', 'canal')) {
            return;
        }

        DB::table('respuesta_solicitudes')
            ->where('canal', HistorialRespuestaCanal::ClienteSj->value)
            ->where('respuesta', 'Solicitud registrada desde el panel cliente (historial retroactivo).')
            ->delete();
    }
};
