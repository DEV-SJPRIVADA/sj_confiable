<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioGestionRequest extends FormRequest
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
            'paterno' => ['required', 'string', 'max:245'],
            'materno' => ['nullable', 'string', 'max:245'],
            'nombre' => ['required', 'string', 'max:245'],
            'telefono' => ['required', 'string', 'max:15'],
            'celular' => ['required', 'string', 'max:15'],
            'correo' => ['required', 'email', 'max:245'],
            'direccion' => ['nullable', 'string', 'max:100'],
            'identificacion' => ['nullable', 'string', 'max:50'],
            'usuario' => ['required', 'string', 'max:245', 'unique:t_usuarios,usuario'],
            'password' => ['required', 'string', 'min:6', 'max:500'],
            'id_rol' => ['required', 'integer', 'exists:t_cat_roles,id_rol'],
            'ciudad' => ['required', 'string', 'max:100'],
            'id_cliente' => ['nullable', 'integer', 'exists:t_clientes,id_cliente'],
            'id_proveedor' => ['nullable', 'integer', 'exists:t_proveedores,id_proveedor'],
            'permiso_ver_documentos' => ['sometimes', 'boolean'],
            'permiso_subir_documentos' => ['sometimes', 'boolean'],
            'permiso_crear_solicitudes' => ['sometimes', 'boolean'],
        ];
    }
}
