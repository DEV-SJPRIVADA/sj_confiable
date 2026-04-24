<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'NIT_proveedor' => ['required', 'integer'],
            'razon_social_proveedor' => ['required', 'string', 'max:50'],
            'nombre_comercial' => ['required', 'string', 'max:50'],
            'correo_proveedor' => ['required', 'string', 'max:50', 'email'],
            'telefono_proveedor' => ['nullable', 'string', 'max:50'],
            'celular_proveedor' => ['required', 'string', 'max:50'],
            'direccion_proveedor' => ['required', 'string', 'max:50'],
            'ciudad_proveedor' => ['required', 'string', 'max:50'],
            'nombre_contacto_proveedor' => ['required', 'string', 'max:50'],
            'cargo_contacto_proveedor' => ['required', 'string', 'max:50'],
        ];
    }
}
