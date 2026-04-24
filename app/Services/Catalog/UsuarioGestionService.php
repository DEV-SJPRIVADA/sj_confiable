<?php

declare(strict_types=1);

namespace App\Services\Catalog;

use App\Domain\Enums\UserRole;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

final class UsuarioGestionService
{
    /**
     * @param  array{paterno:string,materno:?string,nombre:string,telefono:string,celular:string,correo:string,usuario:string,password:string,id_rol:int,ciudad:string,direccion:?string,identificacion:?string,id_cliente:?int,id_proveedor:?int,permiso_ver_documentos:bool|int,permiso_subir_documentos:bool|int,permiso_crear_solicitudes:bool|int,creado_por:int}  $data
     */
    public function crear(array $data): Usuario
    {
        $this->validarRolClienteProveedor($data);

        return DB::transaction(function () use ($data) {
            $persona = Persona::query()->create([
                'paterno' => $data['paterno'],
                'materno' => $data['materno'] ?? '',
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'],
                'celular' => $data['celular'],
                'correo' => $data['correo'],
                'direccion' => $data['direccion'] ?? '',
                'identificacion' => $data['identificacion'] ?? null,
            ]);

            $idCliente = null;
            $idProveedor = null;
            if ((int) $data['id_rol'] === UserRole::Proveedor->value) {
                $idProveedor = (int) $data['id_proveedor'];
            } else {
                $idCliente = isset($data['id_cliente']) ? (int) $data['id_cliente'] : null;
            }

            return Usuario::query()->create([
                'id_rol' => (int) $data['id_rol'],
                'id_persona' => $persona->id_persona,
                'usuario' => $data['usuario'],
                'password' => $data['password'],
                'activo' => 1,
                'ciudad' => $data['ciudad'],
                'fecha_insert' => now()->format('Y-m-d'),
                'id_cliente' => $idCliente,
                'id_proveedor' => $idProveedor,
                'creado_por' => (int) $data['creado_por'],
                'permiso_ver_documentos' => (bool) $data['permiso_ver_documentos'],
                'permiso_subir_documentos' => (bool) $data['permiso_subir_documentos'],
                'permiso_crear_solicitudes' => (bool) $data['permiso_crear_solicitudes'],
            ]);
        });
    }

    /**
     * @param  array{paterno:string,materno:?string,nombre:string,telefono:string,correo:string,usuario:string,id_rol:int,ciudad:string,direccion:?string,celular:string,identificacion:?string,id_cliente:?int,id_proveedor:?int,permiso_ver_documentos:bool|int,permiso_subir_documentos:bool|int,permiso_crear_solicitudes:bool|int,password?:?string}  $data
     */
    public function actualizar(Usuario $usuario, array $data): void
    {
        $this->validarRolClienteProveedor([
            'id_rol' => (int) $data['id_rol'],
            'id_cliente' => $data['id_cliente'] ?? null,
            'id_proveedor' => $data['id_proveedor'] ?? null,
        ]);

        DB::transaction(function () use ($usuario, $data) {
            $persona = $usuario->persona;
            if ($persona === null) {
                throw new \RuntimeException('Usuario sin persona asociada.');
            }
            $persona->update([
                'paterno' => $data['paterno'],
                'materno' => $data['materno'] ?? '',
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'],
                'correo' => $data['correo'],
                'direccion' => $data['direccion'] ?? '',
                'celular' => $data['celular'],
                'identificacion' => $data['identificacion'] ?? null,
            ]);

            $idCliente = null;
            $idProveedor = null;
            if ((int) $data['id_rol'] === UserRole::Proveedor->value) {
                $idProveedor = (int) $data['id_proveedor'];
            } else {
                $idCliente = isset($data['id_cliente']) ? (int) $data['id_cliente'] : null;
            }

            $payload = [
                'id_rol' => (int) $data['id_rol'],
                'usuario' => $data['usuario'],
                'ciudad' => $data['ciudad'],
                'id_cliente' => $idCliente,
                'id_proveedor' => $idProveedor,
                'permiso_ver_documentos' => (bool) $data['permiso_ver_documentos'],
                'permiso_subir_documentos' => (bool) $data['permiso_subir_documentos'],
                'permiso_crear_solicitudes' => (bool) $data['permiso_crear_solicitudes'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = $data['password'];
            }

            $usuario->update($payload);
        });
    }

    /**
     * @param  array{id_rol:int,id_cliente?:?int,id_proveedor?:?int}  $data
     */
    private function validarRolClienteProveedor(array $data): void
    {
        $rol = (int) $data['id_rol'];
        if ($rol === UserRole::Proveedor->value) {
            if (empty($data['id_proveedor'])) {
                throw new \InvalidArgumentException('Debe seleccionar un asociado de negocios para el rol Proveedor.');
            }
        } elseif (! isset($data['id_cliente']) || $data['id_cliente'] === '' || $data['id_cliente'] === null) {
            throw new \InvalidArgumentException('Debe seleccionar un cliente para este rol.');
        }
    }
}
