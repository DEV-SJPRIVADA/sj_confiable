<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
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
            'nit' => ['required', 'integer'],
            'razon_social' => ['required', 'string', 'max:255'],
            'direccion_cliente' => ['nullable', 'string', 'max:255'],
            'ciudad_cliente' => ['nullable', 'string', 'max:50'],
            'telefono_cliente' => ['nullable', 'string', 'max:50'],
            'correo_cliente' => ['nullable', 'email', 'max:255'],
            'nombre' => ['nullable', 'string', 'max:100'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'tipo_cliente' => ['required', 'string', 'max:100'],
        ];
    }
}
