<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Solicitud;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AsignarSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Solicitud|null $solicitud */
        $solicitud = $this->route('solicitud');
        $user = $this->user();

        return $solicitud instanceof Solicitud
            && $user !== null
            && $user->can('assignToProveedor', $solicitud);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'id_proveedor' => [
                'required',
                'integer',
                Rule::exists('t_proveedores', 'id_proveedor'),
            ],
            'cliente_final' => ['nullable', 'string', 'max:150'],
            'tipo_cliente' => ['nullable', 'string', Rule::in(['Interno', 'Externo'])],
            'comentario_asignacion' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function idProveedor(): int
    {
        return (int) $this->validated('id_proveedor');
    }

    public function clienteFinal(): ?string
    {
        $v = $this->validated('cliente_final');

        return $v === null || $v === '' ? null : $v;
    }

    public function tipoCliente(): ?string
    {
        $v = $this->validated('tipo_cliente');

        return $v === null || $v === '' ? null : $v;
    }

    public function comentarioAsignacion(): ?string
    {
        $v = $this->validated('comentario_asignacion');
        if ($v === null || ! is_string($v)) {
            return null;
        }
        $t = trim($v);

        return $t !== '' ? $t : null;
    }
}
