<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Solicitud;
use App\Support\CiudadesColombia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        $solicitud = $this->route('solicitud');

        return $solicitud instanceof Solicitud
            && $this->user() !== null
            && $this->user()->can('update', $solicitud);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'servicio_ids' => array_values(array_filter(
                (array) $this->input('servicio_ids', []),
                static fn (mixed $x): bool => $x !== null && $x !== '',
            )),
        ]);
        if ($this->input('lugar_expedicion') === '') {
            $this->merge(['lugar_expedicion' => null]);
        }
        if ($this->input('telefono_fijo') === '') {
            $this->merge(['telefono_fijo' => null]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $ciudades = CiudadesColombia::opciones();

        return [
            'cliente_final' => ['required', 'string', 'max:100'],
            'servicio_ids' => ['required', 'array', 'min:1', 'max:5'],
            'servicio_ids.*' => ['integer', 'exists:t_cat_servicio,id_servicio'],
            'ciudad_prestacion_servicio' => ['required', 'string', Rule::in($ciudades)],
            'ciudad_solicitud_servicio' => ['required', 'string', Rule::in($ciudades)],
            'nombres' => ['required', 'string', 'max:30'],
            'apellidos' => ['required', 'string', 'max:50'],
            'cargo_candidato' => ['required', 'string', 'max:100'],
            'tipo_identificacion' => ['required', 'string', 'in:CC,CE,PA,NIT,TI,PPT,PEP'],
            'numero_documento' => ['required', 'string', 'max:15'],
            'fecha_expedicion' => ['nullable', 'date'],
            'lugar_expedicion' => ['nullable', 'string', 'max:255', Rule::in($ciudades)],
            'telefono_fijo' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/u'],
            'celular' => ['required', 'string', 'max:30', 'regex:/^[0-9+\s\-]+$/u'],
            'ciudad_residencia_evaluado' => ['required', 'string', Rule::in($ciudades)],
            'direccion_residencia' => ['required', 'string', 'max:50'],
            'comentarios' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'servicio_ids.required' => 'Debe seleccionar entre 1 y 5 servicios.',
            'servicio_ids.min' => 'Debe seleccionar entre 1 y 5 servicios.',
            'servicio_ids.max' => 'Debe seleccionar entre 1 y 5 servicios.',
        ];
    }
}
