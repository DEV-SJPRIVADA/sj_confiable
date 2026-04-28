<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreClienteSolicitudDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'documento' => ['required', 'file', 'mimes:pdf', 'max:15360'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'documento.required' => 'Seleccione un archivo PDF.',
            'documento.mimes' => 'Solo se permiten archivos PDF.',
            'documento.max' => 'El archivo no puede superar 15 MB.',
        ];
    }
}
