<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogos SJ (roles, servicios sueltos y paquetes). Paridad con tabla legado.
 */
final class LegacyCatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('t_cat_roles')) {
            return;
        }

        $roles = [
            [1, 'cliente', 'Es un cliente con permisos'],
            [2, 'admin', 'Es Admin'],
            [3, 'SuperAdmin', 'Administrador del sistema'],
            [4, 'admin_cliente', 'Administrador Cliente'],
            [5, 'cliente_sin_p', 'Es cliente sin permisos'],
            [6, 'Proveedores', 'Proveedores'],
        ];
        foreach ($roles as [$id, $nom, $desc]) {
            DB::table('t_cat_roles')->updateOrInsert(
                ['id_rol' => $id],
                ['nombre' => $nom, 'descripcion' => $desc],
            );
        }

        $servicios = [
            [1, 'Verificacion en base de datos especiales', null],
            [2, 'Verificacion laboral', null],
            [3, 'Verificacion academica', null],
            [4, 'Verificacion personal', null],
            [5, 'Visita domiciliaria presencial', null],
            [6, 'Visita domiciliaria virtual', null],
            [7, 'Poligrafia de pre-empleo', null],
            [8, 'Poligrafia de rutina', null],
            [9, 'Poligrafia especifica', null],
            [10, 'Referencias personales', null],
            [11, 'CIFIN', null],
            [12, 'Prueba VSA', null],
            [13, 'Visita empresarial', null],
            [14, 'Verificacion documental asociado a negocio', null],
            [15, 'Informe socioeconomico', null],
            [16, 'Analisis de riesgos a instalaciones y seguridad fisica', null],
        ];
        foreach ($servicios as [$id, $nom, $d]) {
            DB::table('t_cat_servicio')->updateOrInsert(
                ['id_servicio' => $id],
                ['nombre' => $nom, 'descripcion' => $d],
            );
        }

        $paqs = [
            [1, 'Estudio de confiabilidad hoja de vida completo con cifin - visita domiciliaria presencial', 'Bases de datos, verificación laboral, verificación académica, referencias personales, CIFIN, Visita domiciliaria presencial. Tiempo de respuesta: Cinco (5) días hábiles'],
            [2, 'Estudio de confiabilidad hoja de vida completo con cifin - visita domiciliaria virtual', 'Bases de datos, verificación laboral, verificación académica, referencias personales, CIFIN, Visita domiciliaria virtual. Tiempo de respuesta: Cinco (5) días hábiles'],
            [3, 'Estudio de confiabilidad hoja de vida completo sin cifin con visita domiciliaria presencial', 'Bases de datos, verificación laboral, verificación académica, referencias personales, Visita domiciliaria presencial. Tiempo de respuesta: Cinco (5) días hábiles'],
            [4, 'Estudio de confiabilidad hoja de vida completo sin cifin con visita domiciliaria virtual', 'Bases de datos, verificación laboral, verificación académica, referencias personales, Visita domiciliaria virtual. Tiempo de respuesta: Cinco (5) días hábiles'],
            [5, 'Estudio socioeconomico', 'Visita Domiciliaria, informe socioeconómico, informe ejecutivo. Tiempo de respuesta: Tres (3) días hábiles después de haberse realizado la Visita'],
            [6, 'Estudio de seguridad asociado de negocio con visita', 'Visita a instalaciones insitu, Verificación documental, Verificación en bases de datos especiales: NIT, Representante legal y suplente, Comportamiento financiero'],
            [7, 'Estudio de seguridad asociado de negocio sin visita', 'Verificación documental, Verificación en bases de datos especiales: NIT, Representante legal y suplente, Comportamiento financiero.'],
            [8, 'Sarlaft resolucion 2328 del 2025 superintendencia de transporte', 'La Resolución 2328 del 6 de marzo de 2025, tiene como principal objetivo prevenir el uso del sector de transporte como medio para el lavado de activos y la financiación del terrorismo. Por ello, impone la obligación de implementar el SARLAFT'],
            [9, 'Estudio de confiabilidad hoja de vida completo con visita presencial y poligrafia pre-empleo', "Verificacion laboral, academica, personal, verificacion en base de datos especiales, Visita domiciliaria presencial, poligrafia de pre-empleo\n"],
            [10, 'Estudio de confiabilidad hoja de vida completo con visita virtual y poligrafia pre-empleo', "Verificacion laboral, academica, personal, verificacion en base de datos especiales, Visita domiciliaria virtual, poligrafia de pre-empleo\r\n"],
            [11, 'Estudio de confiabilidad hoja de vida completo con visita online y poligrafia VSA', 'Verificacion laboral, academica, personal, verificacion en base de datos especiales, visita domiciliaria virtual, poligrafia VSA'],
            [12, 'Estudio de confiabilidad hoja de vida completo con visita presencial y poligrafia VSA', 'Verificacion laboral, academica, personal, verificacion en base de datos especiales, visita domiciliaria presencial, poligrafia VSA'],
            [22, 'Estudio de confiabilidad hoja de vida completo con visita virtual y poligrafia pre-empleo', 'Verificacion laboral, academica, personal, verificacion en base de datos especiales, Visita domiciliaria virtual, poligrafia de pre-empleo'],
        ];
        foreach ($paqs as [$id, $nom, $desc]) {
            DB::table('t_paquetes_servicio')->updateOrInsert(
                ['id' => $id],
                ['nombre' => $nom, 'descripcion' => $desc],
            );
        }
    }
}
