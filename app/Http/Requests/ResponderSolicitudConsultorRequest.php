<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Solicitud;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class ResponderSolicitudConsultorRequest extends FormRequest
{
    /** @var list<string> */
    public const ESTADOS_PERMITIDOS = [
        'Nuevo',
        'En proceso',
        'Completado',
        'Cancelado',
        'Registrado',
    ];

    public function authorize(): bool
    {
        /** @var \App\Models\Solicitud|null $solicitud */
        $solicitud = $this->route('solicitud');
        $user = $this->user();

        return $solicitud instanceof Solicitud && $user !== null && $user->can('manageAsConsultor', $solicitud);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nueva_respuesta' => ['required', 'string', 'min:3', 'max:20000'],
            'nuevo_estado' => ['required', 'string', Rule::in(self::ESTADOS_PERMITIDOS)],
            'documentos' => ['sometimes', 'array', 'max:10'],
            'documentos.*' => ['file', 'mimes:pdf', 'max:12288'],
        ];
    }

    public function nuevaRespuestaTexto(): string
    {
        return trim((string) $this->validated('nueva_respuesta'));
    }

    public function nuevoEstado(): string
    {
        return (string) $this->validated('nuevo_estado');
    }

    /**
     * @return list<UploadedFile>
     */
    public function archivosPdf(): array
    {
        $bloque = $this->file('documentos');
        if (! is_array($bloque)) {
            return [];
        }

        $out = [];
        foreach ($bloque as $f) {
            if ($f instanceof UploadedFile) {
                $out[] = $f;
            }
        }

        return $out;
    }
}
