<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Documento;
use App\Models\DocumentoRespuesta;
use App\Models\Solicitud;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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

        return $solicitud instanceof Solicitud
            && $user !== null
            && $user->can('registrarNuevaRespuestaConsultor', $solicitud);
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
            'adjuntos_notificacion' => ['sometimes', 'array'],
            'adjuntos_notificacion.*' => ['string', 'regex:/^(doc|dresp)-[0-9]+$/'],
            'visible_para_cliente' => ['nullable'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var Solicitud|null $solicitud */
            $solicitud = $this->route('solicitud');
            if (! $solicitud instanceof Solicitud) {
                return;
            }
            $refs = $this->input('adjuntos_notificacion', []);
            if (! is_array($refs)) {
                return;
            }
            foreach ($refs as $ref) {
                if (! is_string($ref) || $ref === '') {
                    $v->errors()->add('adjuntos_notificacion', 'Referencia de adjunto no válida.');

                    return;
                }
                if (preg_match('/^doc-(\d+)$/', $ref, $m)) {
                    $ok = Documento::query()
                        ->where('solicitud_id', $solicitud->id)
                        ->whereKey((int) $m[1])
                        ->exists();
                } elseif (preg_match('/^dresp-(\d+)$/', $ref, $m)) {
                    $ok = DocumentoRespuesta::query()
                        ->whereKey((int) $m[1])
                        ->whereHas('respuestaMadre', fn ($q) => $q->where('solicitud_id', $solicitud->id))
                        ->exists();
                } else {
                    $v->errors()->add('adjuntos_notificacion', 'Referencia de adjunto no válida.');

                    return;
                }
                if (! $ok) {
                    $v->errors()->add('adjuntos_notificacion', 'Adjunto no pertenece a esta solicitud.');

                    return;
                }
            }
        });
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

    /**
     * Incluir mensaje en historial/notificación del panel cliente (checkbox + oculto 0/1).
     */
    public function visibleParaOrganizacionCliente(): bool
    {
        return $this->boolean('visible_para_cliente', true);
    }

    /**
     * Referencias `doc-{id}` / `dresp-{id}` para incluir en el aviso a la organización cliente.
     *
     * @return list<string>
     */
    public function refsAdjuntosNotificacion(): array
    {
        $validated = $this->validated();
        $raw = $validated['adjuntos_notificacion'] ?? [];
        if (! is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $r) {
            if (is_string($r) && $r !== '') {
                $out[] = $r;
            }
        }

        return array_values(array_unique($out));
    }
}
