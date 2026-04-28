<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('respuesta_solicitudes', function (Blueprint $table) {
            $table->string('canal', 32)->default('cliente_sj')->after('estado_actual');
            $table->index('canal', 'idx_respuesta_solicitudes_canal');
        });

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                UPDATE respuesta_solicitudes rs
                INNER JOIN t_usuarios u ON u.id_usuario = rs.usuario_id
                SET rs.canal = CASE
                    WHEN u.id_rol = 6 THEN 'sj_proveedor'
                    WHEN u.id_rol IN (1, 4, 5) THEN 'cliente_sj'
                    WHEN u.id_rol IN (2, 3)
                        AND rs.estado_anterior <=> 'Registrado'
                        AND rs.estado_actual <=> 'En proceso'
                        AND TRIM(rs.respuesta) <=> 'En proceso'
                    THEN 'sj_proveedor'
                    WHEN u.id_rol IN (2, 3) THEN 'cliente_sj'
                    ELSE 'cliente_sj'
                END
            ");

            return;
        }

        $rows = DB::table('respuesta_solicitudes as rs')
            ->join('t_usuarios as u', 'u.id_usuario', '=', 'rs.usuario_id')
            ->select('rs.id', 'u.id_rol', 'rs.estado_anterior', 'rs.estado_actual', 'rs.respuesta')
            ->get();

        foreach ($rows as $r) {
            $rid = (int) $r->id_rol;
            $canal = match (true) {
                $rid === 6 => 'sj_proveedor',
                in_array($rid, [1, 4, 5], true) => 'cliente_sj',
                in_array($rid, [2, 3], true)
                    && (string) ($r->estado_anterior ?? '') === 'Registrado'
                    && (string) ($r->estado_actual ?? '') === 'En proceso'
                    && trim((string) ($r->respuesta ?? '')) === 'En proceso' => 'sj_proveedor',
                in_array($rid, [2, 3], true) => 'cliente_sj',
                default => 'cliente_sj',
            };

            DB::table('respuesta_solicitudes')->where('id', $r->id)->update(['canal' => $canal]);
        }
    }

    public function down(): void
    {
        Schema::table('respuesta_solicitudes', function (Blueprint $table) {
            $table->dropIndex(['canal']);
            $table->dropColumn('canal');
        });
    }
};
