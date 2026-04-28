<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Clientes, personas, proveedor y usuarios del dump legado de prueba (paridad bootstrap referencia).
 */
final class LegacyIdentitySeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('t_usuarios')) {
            return;
        }

        DB::table('t_clientes')->updateOrInsert(['id_cliente' => 1], [
            'NIT' => 900576718,
            'razon_social' => 'SJ SEGURIDAD PRIVADA LTDA',
            'direccion_cliente' => 'AV 4N #26N - 39',
            'ciudad_cliente' => 'Cali',
            'telefono_cliente' => '3186324112',
            'correo_cliente' => 'contacto@sjsp.com.co',
            'activo' => 1,
            'nombre' => 'Wilfredo Velez',
            'cargo' => 'Gerente General',
            'tipo_cliente' => 'Grupo',
        ]);
        DB::table('t_clientes')->updateOrInsert(['id_cliente' => 2], [
            'NIT' => 900576718,
            'razon_social' => 'Sj Seguridad Privada',
            'direccion_cliente' => 'AV 4N #26N - 39',
            'ciudad_cliente' => 'Cali',
            'telefono_cliente' => '3156703771',
            'correo_cliente' => 'contacto@sjsp.com.co',
            'activo' => 1,
            'nombre' => 'Luisa Ferrerosa',
            'cargo' => 'Directora comercial',
            'tipo_cliente' => 'Grupo',
        ]);

        $personas = [
            [1, 'Mendez', null, 'Duvan', '3103618679', 'programador.tic@sjsp.com.co', '1005936099', '2025-09-11 21:33:14', '3103618679', 'AV 4N #26N-39'],
            [2, 'Garrido', '', 'Lorena', '3174029444', 'coordinacion.personal@sjsp.com.co', '66916986', '2025-09-12 14:59:48', '3174029444', 'Av 4N #26N - 39'],
            [3, 'Vidal', '', 'Jesus', '3156204196', 'ejecutivocomercial5@sjsp.com.co', '18463965', '2025-09-12 15:10:43', '3156204196', 'Av 4N #26N-39'],
            [4, 'Aristizabal', '', 'David', '3155938631', 'analistaseleccion@sjsp.com.co', '1143861992', '2025-09-12 15:29:04', '3155938631', 'Av 4N #26N - 39'],
            [5, 'Ferrerosa', null, 'Luisa', '3186128406', 'comercial@sjsp.com.co', null, '2025-09-12 15:37:48', '3186128406', 'Av 4N #26N - 39'],
            [6, 'Alzate', '', 'Nataly', '3156496859', 'asistentecomercial@sjsp.com.co', '1151963257', '2025-09-12 16:07:52', '3156496859', 'Av 4N #26N - 39'],
            [7, 'Prueba', '', 'Prueba', '3103618679', 'duvanmenddez2001@gmail.com', '99999999999', '2025-09-26 14:03:10', '3174029444', 'Av 4N #26N - 39'],
        ];
        foreach ($personas as $p) {
            DB::table('t_persona')->updateOrInsert(['id_persona' => $p[0]], [
                'paterno' => $p[1],
                'materno' => $p[2],
                'nombre' => $p[3],
                'telefono' => $p[4],
                'correo' => $p[5],
                'identificacion' => $p[6],
                'fechaInsert' => $p[7],
                'celular' => $p[8],
                'direccion' => $p[9],
            ]);
        }

        DB::table('t_proveedores')->updateOrInsert(['id_proveedor' => 7], [
            'NIT_proveedor' => 900518300,
            'razon_social_proveedor' => 'CENTRAL TRUTH SAS',
            'nombre_comercial' => 'CENTRAL TRUTH SAS',
            'correo_proveedor' => 'duvanmendez.2001@hotmail.es',
            'telefono_proveedor' => '3113002294',
            'celular_proveedor' => '3336025200',
            'direccion_proveedor' => 'AV 6A BIS # 35 N 100 OFIC 410',
            'ciudad_proveedor' => 'Bogotá',
            'nombre_contacto_proveedor' => 'RUBEN DARIO PERLAZA',
            'cargo_contacto_proveedor' => 'Gerente',
        ]);

        $usuarios = [
            [
                'id_usuario' => 1,
                'id_rol' => 3,
                'id_persona' => 1,
                'usuario' => 'Administrador',
                'password' => '$2y$10$XwAu7doZO6CKpEJOh.NG0O827OFsMYGPfi8jcsEi5hCJrpJsuIzTe',
                'activo' => 1,
                'ciudad' => 'Cali',
                'fecha_insert' => '2025-09-11',
                'estado_conexion' => 'Activo',
                'id_cliente' => 2,
                'creado_por' => 1,
                'reset_token' => null,
                'reset_token_expiry' => null,
                'id_proveedor' => null,
                'permiso_ver_documentos' => 0,
                'permiso_subir_documentos' => 0,
                'permiso_crear_solicitudes' => 0,
            ],
            [
                'id_usuario' => 2,
                'id_rol' => 4,
                'id_persona' => 2,
                'usuario' => 'Lorena Garrido',
                'password' => '$2y$10$HmcN95gPiq7f/nBGPRuSROQQtvo1wiasqDfcyWla.dAICeYRotsX.',
                'activo' => 1,
                'ciudad' => 'Cali',
                'fecha_insert' => '2025-09-12',
                'estado_conexion' => 'Desconectado',
                'id_cliente' => 1,
                'creado_por' => 1,
                'reset_token' => null,
                'reset_token_expiry' => null,
                'id_proveedor' => null,
                'permiso_ver_documentos' => 0,
                'permiso_subir_documentos' => 0,
                'permiso_crear_solicitudes' => 0,
            ],
            [
                'id_usuario' => 3,
                'id_rol' => 2,
                'id_persona' => 3,
                'usuario' => 'Consultor 1',
                'password' => '$2y$10$4oHuZ./8NcGUMtTwYE82q.81EvLGpxksX73itGCDqcihKKJKwNcQm',
                'activo' => 1,
                'ciudad' => 'Cali',
                'fecha_insert' => '2025-09-12',
                'estado_conexion' => 'Desconectado',
                'id_cliente' => 2,
                'creado_por' => 1,
                'reset_token' => null,
                'reset_token_expiry' => null,
                'id_proveedor' => null,
                'permiso_ver_documentos' => 0,
                'permiso_subir_documentos' => 0,
                'permiso_crear_solicitudes' => 0,
            ],
            [
                'id_usuario' => 4,
                'id_rol' => 1,
                'id_persona' => 4,
                'usuario' => 'analistaseleccion@sjsp.com.co',
                'password' => '$2y$10$3sGG8drFB3HbevULjDbDO.geRaRIFa6BXe7ywEqbAgQPNzuV.vXNy',
                'activo' => 1,
                'ciudad' => 'Cali',
                'fecha_insert' => '2025-09-12',
                'estado_conexion' => 'Desconectado',
                'id_cliente' => 1,
                'creado_por' => 1,
                'reset_token' => null,
                'reset_token_expiry' => null,
                'id_proveedor' => null,
                'permiso_ver_documentos' => 1,
                'permiso_subir_documentos' => 1,
                'permiso_crear_solicitudes' => 1,
            ],
            [
                'id_usuario' => 5,
                'id_rol' => 3,
                'id_persona' => 5,
                'usuario' => 'Luisa Ferrerosa',
                'password' => '$2y$10$LhQY9bvPiUru91Iahs/Dnu3FXuuWj4WvIsKltneLpQwJq1e4V5pj2',
                'activo' => 1,
                'ciudad' => 'Cali',
                'fecha_insert' => '2025-09-12',
                'estado_conexion' => 'Activo',
                'id_cliente' => 2,
                'creado_por' => 1,
                'reset_token' => null,
                'reset_token_expiry' => null,
                'id_proveedor' => null,
                'permiso_ver_documentos' => 0,
                'permiso_subir_documentos' => 0,
                'permiso_crear_solicitudes' => 0,
            ],
            [
                'id_usuario' => 6,
                'id_rol' => 2,
                'id_persona' => 6,
                'usuario' => 'Consultor 2',
                'password' => '$2y$10$3QfLK1S/B959HmgYrtxmvex5aLaaXZ7zjNVo4rnLCrLAAluB7i/I.',
                'activo' => 1,
                'ciudad' => 'Cali',
                'fecha_insert' => '2025-09-12',
                'estado_conexion' => 'Desconectado',
                'id_cliente' => 2,
                'creado_por' => 1,
                'reset_token' => null,
                'reset_token_expiry' => null,
                'id_proveedor' => null,
                'permiso_ver_documentos' => 0,
                'permiso_subir_documentos' => 0,
                'permiso_crear_solicitudes' => 0,
            ],
            [
                'id_usuario' => 7,
                'id_rol' => 4,
                'id_persona' => 7,
                'usuario' => 'Prueba',
                'password' => '$2y$10$ewWq4y27kCbHzJuRuI2woeqpt6EUOiv/cbn0/y4MnKDaestmiYPLa',
                'activo' => 1,
                'ciudad' => 'Cali',
                'fecha_insert' => '2025-09-26',
                'estado_conexion' => 'Desconectado',
                'id_cliente' => 2,
                'creado_por' => 1,
                'reset_token' => null,
                'reset_token_expiry' => null,
                'id_proveedor' => null,
                'permiso_ver_documentos' => 0,
                'permiso_subir_documentos' => 0,
                'permiso_crear_solicitudes' => 0,
            ],
        ];
        foreach ($usuarios as $row) {
            $id = $row['id_usuario'];
            unset($row['id_usuario']);
            DB::table('t_usuarios')->updateOrInsert(['id_usuario' => $id], $row);
        }
    }
}
