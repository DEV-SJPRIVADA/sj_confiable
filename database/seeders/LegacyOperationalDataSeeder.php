<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Datos operativos del dump legado (solicitudes, respuestas, notificaciones, solicitudes de usuario).
 */
final class LegacyOperationalDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('solicitudes')) {
            return;
        }

        if (filter_var((string) env('SEED_LEGACY_OPERATIONAL', true), FILTER_VALIDATE_BOOL) === false) {
            return;
        }

        $this->seedSolicitudes();
        $this->seedRespuestaMadre();
        $this->seedRespuestaSolicitudes();
        $this->seedDocumentosRespuesta();
        $this->seedNotificaciones();
        $this->seedNotificacionesCliente();
        $this->seedSolicitudesUsuario();
    }

    private function seedSolicitudes(): void
    {
        $filas = [
            [
                'id' => 1,
                'empresa_solicitante' => '',
                'nit_empresa_solicitante' => '',
                'cliente_final' => null,
                'tipo_cliente' => null,
                'servicio_id' => null,
                'paquete_id' => 22,
                'ciudad_prestacion_servicio' => 'Cali',
                'ciudad_solicitud_servicio' => 'Cali',
                'nombres' => 'DIEGO ANDRES ',
                'apellidos' => 'ROSAS RAMIREZ ',
                'tipo_identificacion' => 'CC',
                'numero_documento' => '80091085',
                'fecha_expedicion' => '2013-08-28',
                'lugar_expedicion' => 'Cali',
                'telefono_fijo' => '3248653453',
                'celular' => '3248653453',
                'ciudad_residencia_evaluado' => 'Cali',
                'direccion_residencia' => 'Carrera 17 a # 26-31',
                'cargo_candidato' => 'JEFE DE SEGURIDAD',
                'comentarios' => '',
                'fecha_creacion' => '2025-10-02 14:57:51',
                'usuario_id' => 4,
                'estado' => 'Completado',
                'activo' => 1,
                'id_proveedor' => null,
                'fecha_asignacion_proveedor' => null,
            ],
            [
                'id' => 2,
                'empresa_solicitante' => '',
                'nit_empresa_solicitante' => '',
                'cliente_final' => null,
                'tipo_cliente' => null,
                'servicio_id' => null,
                'paquete_id' => 22,
                'ciudad_prestacion_servicio' => 'Sincelejo',
                'ciudad_solicitud_servicio' => 'Sincelejo',
                'nombres' => 'OSCAR IVAN ',
                'apellidos' => 'NIÑO CARDENAS ',
                'tipo_identificacion' => 'CC',
                'numero_documento' => '1099212066',
                'fecha_expedicion' => '2012-05-15',
                'lugar_expedicion' => 'Sincelejo',
                'telefono_fijo' => '3218106580',
                'celular' => '3218106580',
                'ciudad_residencia_evaluado' => 'Sincelejo',
                'direccion_residencia' => 'Calle 31b # 14b-16',
                'cargo_candidato' => 'JEFE DE SEGURIDAD',
                'comentarios' => '',
                'fecha_creacion' => '2025-10-02 15:02:27',
                'usuario_id' => 4,
                'estado' => 'Completado',
                'activo' => 1,
                'id_proveedor' => null,
                'fecha_asignacion_proveedor' => null,
            ],
            [
                'id' => 3,
                'empresa_solicitante' => '',
                'nit_empresa_solicitante' => '',
                'cliente_final' => null,
                'tipo_cliente' => null,
                'servicio_id' => null,
                'paquete_id' => 22,
                'ciudad_prestacion_servicio' => 'Valledupar',
                'ciudad_solicitud_servicio' => 'Valledupar',
                'nombres' => 'HEYERIS RAFAEL',
                'apellidos' => 'ANCHILA DELUQUE',
                'tipo_identificacion' => 'CC',
                'numero_documento' => '72268282',
                'fecha_expedicion' => '2000-07-18',
                'lugar_expedicion' => 'Barranquilla',
                'telefono_fijo' => '3206650190',
                'celular' => '3206650190',
                'ciudad_residencia_evaluado' => 'Valledupar',
                'direccion_residencia' => 'Calle 9 # 29-37',
                'cargo_candidato' => 'JEFE DE SEGURIDAD',
                'comentarios' => '',
                'fecha_creacion' => '2025-10-02 15:16:59',
                'usuario_id' => 4,
                'estado' => 'Completado',
                'activo' => 1,
                'id_proveedor' => null,
                'fecha_asignacion_proveedor' => null,
            ],
            [
                'id' => 4,
                'empresa_solicitante' => 'red de servicios del cauca',
                'nit_empresa_solicitante' => '',
                'cliente_final' => 'Red de servicios del Cauca',
                'tipo_cliente' => null,
                'servicio_id' => null,
                'paquete_id' => 22,
                'ciudad_prestacion_servicio' => 'Popayán',
                'ciudad_solicitud_servicio' => 'Popayán',
                'nombres' => 'DAVID ANDRES ',
                'apellidos' => 'OREJUELA PITO',
                'tipo_identificacion' => 'CC',
                'numero_documento' => '1061696776',
                'fecha_expedicion' => '2005-04-25',
                'lugar_expedicion' => 'Popayán',
                'telefono_fijo' => '58585858',
                'celular' => '3108330461',
                'ciudad_residencia_evaluado' => 'Popayán',
                'direccion_residencia' => 'carrera 21c # 15-35',
                'cargo_candidato' => 'ESCOLTA',
                'comentarios' => '',
                'fecha_creacion' => '2025-10-06 16:47:04',
                'usuario_id' => 4,
                'estado' => 'En proceso',
                'activo' => 1,
                'id_proveedor' => null,
                'fecha_asignacion_proveedor' => null,
            ],
        ];

        foreach ($filas as $row) {
            $id = $row['id'];
            unset($row['id']);
            DB::table('solicitudes')->updateOrInsert(['id' => $id], $row);
        }

        $this->mysqlOnlyAutoIncrement('solicitudes', 5);
    }

    private function seedRespuestaMadre(): void
    {
        $filas = [
            [
                'id' => 2,
                'solicitud_id' => 1,
                'usuario_id' => 6,
                'respuesta' => "Se adjunta informe de poligrafía, esta pendiente el informe de estudio de confiabilidad (07-10-25). \r\nSe adjuntar informe de estudio de confiabilidad",
                'estado_actual' => 'Completado',
                'fecha_creacion' => '2025-10-07 21:48:18',
            ],
            [
                'id' => 3,
                'solicitud_id' => 2,
                'usuario_id' => 6,
                'respuesta' => "Remito informe de poligrafia, esta pendiente el informe del estudio de confiabilidad  (07-10-25)\r\nRemito informe de estudio de confiabilidad del candidato (08-10-25)",
                'estado_actual' => 'Completado',
                'fecha_creacion' => '2025-10-08 13:33:33',
            ],
            [
                'id' => 4,
                'solicitud_id' => 3,
                'usuario_id' => 6,
                'respuesta' => "Remito informe de poligrafía, queda pendiente el informe de estudio de confiabilidad (07-10-25)\r\nRemito informe de estudio de confiabilidad del candidato (08-10-25)",
                'estado_actual' => 'Completado',
                'fecha_creacion' => '2025-10-08 13:34:12',
            ],
            [
                'id' => 6,
                'solicitud_id' => 4,
                'usuario_id' => 6,
                'respuesta' => 'Remito informe de poligrafía, esta pendiente el informe del estudio de confiabilidad (9-10-25)',
                'estado_actual' => 'En proceso',
                'fecha_creacion' => '2025-10-09 14:39:25',
            ],
        ];

        foreach ($filas as $row) {
            $id = $row['id'];
            unset($row['id']);
            DB::table('respuesta_madre')->updateOrInsert(['id' => $id], $row);
        }

        $this->mysqlOnlyAutoIncrement('respuesta_madre', 7);
    }

    private function seedRespuestaSolicitudes(): void
    {
        $filas = [
            [
                'id' => 3,
                'solicitud_id' => 1,
                'usuario_id' => 6,
                'respuesta' => 'Se adjunta informe de poligrafía, esta pendiente el informe de estudio de confiabilidad (07-10-25)',
                'documento_respuesta' => null,
                'fecha_respuesta' => '2025-10-07 12:57:39',
                'estado_anterior' => 'Nuevo',
                'estado_actual' => 'En proceso',
            ],
            [
                'id' => 4,
                'solicitud_id' => 2,
                'usuario_id' => 6,
                'respuesta' => 'Remito informe de poligrafia, esta pendiente el informe del estudio de confiabilidad  (07-10-25)',
                'documento_respuesta' => null,
                'fecha_respuesta' => '2025-10-07 20:43:23',
                'estado_anterior' => 'Nuevo',
                'estado_actual' => 'En proceso',
            ],
            [
                'id' => 5,
                'solicitud_id' => 3,
                'usuario_id' => 6,
                'respuesta' => 'Remito informe de poligrafía, queda pendiente el informe de estudio de confiabilidad (07-10-25)',
                'documento_respuesta' => null,
                'fecha_respuesta' => '2025-10-07 20:44:13',
                'estado_anterior' => 'Nuevo',
                'estado_actual' => 'En proceso',
            ],
            [
                'id' => 6,
                'solicitud_id' => 1,
                'usuario_id' => 6,
                'respuesta' => "Se adjunta informe de poligrafía, esta pendiente el informe de estudio de confiabilidad (07-10-25). \r\nSe adjuntar informe de estudio de confiabilidad",
                'documento_respuesta' => null,
                'fecha_respuesta' => '2025-10-07 21:48:16',
                'estado_anterior' => 'En proceso',
                'estado_actual' => 'Completado',
            ],
            [
                'id' => 8,
                'solicitud_id' => 2,
                'usuario_id' => 6,
                'respuesta' => "Remito informe de poligrafia, esta pendiente el informe del estudio de confiabilidad  (07-10-25)\r\nRemito informe de estudio de confiabilidad del candidato (08-10-25)",
                'documento_respuesta' => null,
                'fecha_respuesta' => '2025-10-08 13:33:31',
                'estado_anterior' => 'En proceso',
                'estado_actual' => 'Completado',
            ],
            [
                'id' => 9,
                'solicitud_id' => 3,
                'usuario_id' => 6,
                'respuesta' => "Remito informe de poligrafía, queda pendiente el informe de estudio de confiabilidad (07-10-25)\r\nRemito informe de estudio de confiabilidad del candidato (08-10-25)",
                'documento_respuesta' => null,
                'fecha_respuesta' => '2025-10-08 13:34:10',
                'estado_anterior' => 'En proceso',
                'estado_actual' => 'Completado',
            ],
            [
                'id' => 10,
                'solicitud_id' => 4,
                'usuario_id' => 6,
                'respuesta' => 'Remito informe de poligrafía, esta pendiente el informe del estudio de confiabilidad (9-10-25)',
                'documento_respuesta' => null,
                'fecha_respuesta' => '2025-10-09 14:39:23',
                'estado_anterior' => 'Nuevo',
                'estado_actual' => 'En proceso',
            ],
        ];

        $filas = array_map(static function (array $r): array {
            $r['canal'] = 'sj_proveedor';

            return $r;
        }, $filas);

        foreach ($filas as $row) {
            $id = $row['id'];
            unset($row['id']);
            DB::table('respuesta_solicitudes')->updateOrInsert(['id' => $id], $row);
        }

        $this->mysqlOnlyAutoIncrement('respuesta_solicitudes', 11);
    }

    private function seedDocumentosRespuesta(): void
    {
        $filas = [
            [2, 2, 'SEGURIDAD SJ - 06102025 - DIEGO ANDRES ROSAS RAMÍREZ - PRE.pdf', 'resp_68e50e45e3fa42.13658242.pdf', '2025-10-07 12:57:41'],
            [3, 3, 'OSCAR IVAN NIÑO CARDENAS.docx.pdf', 'resp_68e57b6c7b5872.92804712.pdf', '2025-10-07 20:43:24'],
            [4, 4, 'HEYERIS RAFAEL ANCHILA DELUQUE.docx.pdf', 'resp_68e57b9edcb054.36245959.pdf', '2025-10-07 20:44:14'],
            [5, 2, 'CIERRE ESTUDIO DE CONFIABILIDAD - DIEGO ANDRES ROSAS RAMIREZ.pdf', 'resp_68e58aa2863a97.66028626.pdf', '2025-10-07 21:48:18'],
            [6, 3, 'CIERRE ESTUDIO DE CONFIABILIDAD - OSCAR IVAN NIÑO CARDENAS..pdf', 'resp_68e6682d592895.36334117.pdf', '2025-10-08 13:33:33'],
            [7, 4, 'CIERRE ESTUDIO DE CONFIABILIDAD - HEYERIS RAFAEL ANCHILA DE LUQUE.pdf', 'resp_68e668548689b6.46475758.pdf', '2025-10-08 13:34:12'],
            [8, 6, 'DAVID ANDRES OREJUELA PITO.pdf', 'resp_68e7c91d1b9ba9.71146725.pdf', '2025-10-09 14:39:25'],
        ];

        foreach ($filas as [$id, $madreId, $nombre, $ruta, $fecha]) {
            DB::table('documentos_respuesta')->updateOrInsert(
                ['id' => $id],
                [
                    'respuesta_madre_id' => $madreId,
                    'nombre_documentoResp' => $nombre,
                    'ruta_documentoResp' => $ruta,
                    'fecha_subidaResp' => $fecha,
                ],
            );
        }

        $this->mysqlOnlyAutoIncrement('documentos_respuesta', 9);
    }

    private function seedNotificaciones(): void
    {
        $filas = [
            [1, 'confiabilidad', 'Sj Seguridad Privada', 1, 'El cliente Sj Seguridad Privada ha subido la solicitud #1 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-09-26 14:46:32'],
            [2, 'confiabilidad', 'Sj Seguridad Privada', 1, 'El cliente Sj Seguridad Privada ha subido la solicitud #1 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-09-26 14:46:36'],
            [3, 'usuarios', 'Sj Seguridad Privada', 1, 'El cliente Sj Seguridad Privada ha subido la solicitud #1 de usuarios. Dar clic para más detalle.', 2, 1, '2025-09-30 21:47:59'],
            [4, 'usuarios', 'Sj Seguridad Privada', 1, 'El cliente Sj Seguridad Privada ha subido la solicitud #1 de usuarios. Dar clic para más detalle.', 3, 1, '2025-09-30 21:48:02'],
            [5, 'confiabilidad', 'Sj Seguridad Privada', 2, 'El cliente Sj Seguridad Privada ha subido la solicitud #2 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-01 20:28:34'],
            [6, 'confiabilidad', 'Sj Seguridad Privada', 2, 'El cliente Sj Seguridad Privada ha subido la solicitud #2 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-01 20:28:37'],
            [7, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #3 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-01 20:51:13'],
            [8, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #3 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-01 20:51:16'],
            [9, 'confiabilidad', 'Sj Seguridad Privada', 4, 'El cliente Sj Seguridad Privada ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-02 14:21:26'],
            [10, 'confiabilidad', 'Sj Seguridad Privada', 4, 'El cliente Sj Seguridad Privada ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-02 14:21:29'],
            [11, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 1, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #1 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-02 19:57:51'],
            [12, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 1, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #1 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-02 19:57:54'],
            [13, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 2, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #2 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-02 20:02:27'],
            [14, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 2, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #2 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-02 20:02:31'],
            [15, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #3 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-02 20:16:59'],
            [16, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #3 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-02 20:17:02'],
            [17, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 2, 0, '2025-10-06 21:47:04'],
            [18, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-06 21:47:07'],
            [19, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 2, 0, '2025-10-08 15:29:17'],
            [20, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-08 15:29:20'],
        ];

        foreach ($filas as $r) {
            DB::table('notificaciones')->updateOrInsert(
                ['id' => $r[0]],
                [
                    'tipo' => $r[1],
                    'cliente_nombre' => $r[2],
                    'id_solicitud' => $r[3],
                    'mensaje' => $r[4],
                    'rol_destino' => $r[5],
                    'leido' => $r[6],
                    'fecha' => $r[7],
                ],
            );
        }

        $this->mysqlOnlyAutoIncrement('notificaciones', 21);
    }

    private function seedNotificacionesCliente(): void
    {
        $filas = [
            [1, 'Poligrafia de pre-empleo', 'Sj Seguridad Privada', 2, 'Su solicitud #2 de Poligrafia de pre-empleo ha recibido una nueva respuesta. Nuevo estado: En proceso.', 7, 1, '2025-10-01 20:34:59'],
            [2, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'Su solicitud #3 de solicitud ha recibido una nueva respuesta. Estado: Completado.', 4, 1, '2025-10-01 20:54:13'],
            [3, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 1, 'Su solicitud #1 de solicitud ha recibido una nueva respuesta. Estado: En proceso.', 4, 0, '2025-10-07 12:57:39'],
            [4, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 2, 'Su solicitud #2 de solicitud ha recibido una nueva respuesta. Estado: En proceso.', 4, 0, '2025-10-07 20:43:23'],
            [5, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'Su solicitud #3 de solicitud ha recibido una nueva respuesta. Estado: En proceso.', 4, 0, '2025-10-07 20:44:13'],
            [6, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 1, 'Su solicitud #1 de solicitud ha recibido una nueva respuesta. Estado: Completado.', 4, 0, '2025-10-07 21:48:16'],
            [7, 'Poligrafia de pre-empleo', 'Sj Seguridad Privada', 2, 'Su solicitud #2 de Poligrafia de pre-empleo ha recibido una nueva respuesta. Estado: Completado.', 7, 1, '2025-10-07 21:48:44'],
            [8, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 2, 'Su solicitud #2 de solicitud ha recibido una nueva respuesta. Estado: Completado.', 4, 0, '2025-10-08 13:33:31'],
            [9, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'Su solicitud #3 de solicitud ha recibido una nueva respuesta. Estado: Completado.', 4, 0, '2025-10-08 13:34:10'],
            [10, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'Su solicitud #4 de solicitud ha recibido una nueva respuesta. Estado: En proceso.', 4, 0, '2025-10-09 14:39:23'],
            [11, 'Referencias personales', 'Sj Seguridad Privada', 1, 'Su solicitud #1 de Referencias personales ha recibido una nueva respuesta. Estado: En proceso.', 7, 0, '2025-10-09 20:38:52'],
        ];

        foreach ($filas as $r) {
            DB::table('notificaciones_cliente')->updateOrInsert(
                ['id' => $r[0]],
                [
                    'tipo' => $r[1],
                    'cliente_nombre' => $r[2],
                    'id_solicitud' => $r[3],
                    'mensaje' => $r[4],
                    'id_usuario_destino' => $r[5],
                    'leido' => $r[6],
                    'fecha' => $r[7],
                ],
            );
        }

        $this->mysqlOnlyAutoIncrement('notificaciones_cliente', 12);
    }

    private function seedSolicitudesUsuario(): void
    {
        DB::table('t_solicitudes_usuario')->updateOrInsert(
            ['id_solicitud' => 1],
            [
                'id_cliente' => 2,
                'id_usuario_solicitante' => 7,
                'tipo' => 'Modificar',
                'datos_usuario' => json_encode([
                    'idUsuario' => '7',
                    'motivo' => 'Modificacion',
                ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'estado' => 'Pendiente',
                'fecha_solicitud' => '2025-09-30 21:47:59',
                'fecha_respuesta' => null,
                'id_usuario_responde' => null,
                'comentario_respuesta' => null,
            ],
        );
    }

    private function mysqlOnlyAutoIncrement(string $table, int $next): void
    {
        $driver = DB::connection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE `'.$table.'` AUTO_INCREMENT = '.(string) $next);
    }
}

