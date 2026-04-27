<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('panel.consultor.asociados.index', array_merge(
                    $this->queryForRedirectBack(),
                    ['open_modal' => 'crear'],
                ))
                ->withInput()
                ->with('open_modal', 'crear')
                ->withErrors($validator)
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function queryForRedirectBack(): array
    {
        return array_filter(
            $this->only(['per_page', 'q', 'sort', 'dir']),
            static fn (mixed $v): bool => $v !== null && $v !== '',
        );
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
