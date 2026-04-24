<?php

declare(strict_types=1);

namespace App\Services\Catalog;

use App\Models\Proveedor;
final class ProveedorCatalogService
{
    /**
     * @param  array{NIT_proveedor:int|string,razon_social_proveedor:string,nombre_comercial:string,correo_proveedor:string,telefono_proveedor:?string,celular_proveedor:string,direccion_proveedor:string,ciudad_proveedor:string,nombre_contacto_proveedor:string,cargo_contacto_proveedor:string}  $data
     */
    public function crear(array $data): Proveedor
    {
        return Proveedor::query()->create([
            'NIT_proveedor' => (int) $data['NIT_proveedor'],
            'razon_social_proveedor' => $data['razon_social_proveedor'],
            'nombre_comercial' => $data['nombre_comercial'],
            'correo_proveedor' => $data['correo_proveedor'],
            'telefono_proveedor' => $data['telefono_proveedor'] ?? null,
            'celular_proveedor' => $data['celular_proveedor'],
            'direccion_proveedor' => $data['direccion_proveedor'],
            'ciudad_proveedor' => $data['ciudad_proveedor'],
            'nombre_contacto_proveedor' => $data['nombre_contacto_proveedor'],
            'cargo_contacto_proveedor' => $data['cargo_contacto_proveedor'],
        ]);
    }

    /**
     * @param  array{NIT_proveedor:int|string,razon_social_proveedor:string,nombre_comercial:string,correo_proveedor:string,telefono_proveedor:?string,celular_proveedor:string,direccion_proveedor:string,ciudad_proveedor:string,nombre_contacto_proveedor:string,cargo_contacto_proveedor:string}  $data
     */
    public function actualizar(Proveedor $proveedor, array $data): void
    {
        $proveedor->update([
            'NIT_proveedor' => (int) $data['NIT_proveedor'],
            'razon_social_proveedor' => $data['razon_social_proveedor'],
            'nombre_comercial' => $data['nombre_comercial'],
            'correo_proveedor' => $data['correo_proveedor'],
            'telefono_proveedor' => $data['telefono_proveedor'] ?? null,
            'celular_proveedor' => $data['celular_proveedor'],
            'direccion_proveedor' => $data['direccion_proveedor'],
            'ciudad_proveedor' => $data['ciudad_proveedor'],
            'nombre_contacto_proveedor' => $data['nombre_contacto_proveedor'],
            'cargo_contacto_proveedor' => $data['cargo_contacto_proveedor'],
        ]);
    }

    public function eliminar(Proveedor $proveedor): void
    {
        if ($proveedor->solicitudes()->exists()) {
            throw new \DomainException('No se puede eliminar: hay solicitudes asociadas a este asociado.');
        }
        if ($proveedor->usuarios()->exists()) {
            throw new \DomainException('No se puede eliminar: hay usuarios vinculados a este asociado.');
        }
        $proveedor->delete();
    }
}
