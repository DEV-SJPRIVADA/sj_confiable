<?php

declare(strict_types=1);

namespace App\Services\Catalog;

use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

final class ClienteCatalogService
{
    /**
     * @param  array{nit:int|string,razon_social:string,direccion_cliente:?string,ciudad_cliente:?string,telefono_cliente:?string,correo_cliente:?string,nombre:?string,cargo:?string,tipo_cliente:string}  $data
     */
    public function crear(array $data): Cliente
    {
        return Cliente::query()->create([
            'NIT' => (int) $data['nit'],
            'razon_social' => $data['razon_social'],
            'direccion_cliente' => $data['direccion_cliente'] ?? null,
            'ciudad_cliente' => $data['ciudad_cliente'] ?? null,
            'telefono_cliente' => $data['telefono_cliente'] ?? null,
            'correo_cliente' => $data['correo_cliente'] ?? null,
            'nombre' => $data['nombre'] ?? null,
            'cargo' => $data['cargo'] ?? null,
            'tipo_cliente' => $data['tipo_cliente'],
            'activo' => 1,
        ]);
    }

    /**
     * @param  array{nit:int|string,razon_social:string,direccion_cliente:?string,ciudad_cliente:?string,telefono_cliente:?string,correo_cliente:?string,nombre:?string,cargo:?string,tipo_cliente:string}  $data
     */
    public function actualizar(Cliente $cliente, array $data): void
    {
        $cliente->update([
            'NIT' => (int) $data['nit'],
            'razon_social' => $data['razon_social'],
            'direccion_cliente' => $data['direccion_cliente'] ?? null,
            'ciudad_cliente' => $data['ciudad_cliente'] ?? null,
            'telefono_cliente' => $data['telefono_cliente'] ?? null,
            'correo_cliente' => $data['correo_cliente'] ?? null,
            'nombre' => $data['nombre'] ?? null,
            'cargo' => $data['cargo'] ?? null,
            'tipo_cliente' => $data['tipo_cliente'],
        ]);
    }

    public function alternarActivo(Cliente $cliente): void
    {
        DB::transaction(function () use ($cliente) {
            $nuevo = (int) $cliente->activo === 1 ? 0 : 1;
            $cliente->update(['activo' => $nuevo]);
            $cliente->usuarios()->update(['activo' => $nuevo]);
        });
    }
}
