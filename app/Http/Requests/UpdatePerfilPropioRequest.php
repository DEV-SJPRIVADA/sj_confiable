<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdatePerfilPropioRequest extends FormRequest
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
        $personaId = (int) $this->user()?->id_persona;

        return [
            'paterno' => ['required', 'string', 'max:245'],
            'materno' => ['nullable', 'string', 'max:245'],
            'nombre' => ['required', 'string', 'max:245'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'celular' => ['required', 'string', 'max:15'],
            'correo' => [
                'required',
                'string',
                'max:245',
                'email',
                Rule::unique('t_persona', 'correo')->ignore($personaId, 'id_persona'),
            ],
            'direccion' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('panel.consultor.perfil.show')
                ->withInput()
                ->with('open_perfil_modal', true)
                ->withErrors($validator)
        );
    }
}
