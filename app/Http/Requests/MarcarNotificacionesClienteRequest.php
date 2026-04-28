<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MarcarNotificacionesClienteRequest extends FormRequest
{
    private const ROLES_CLIENTE = [1, 4, 5];

    public function authorize(): bool
    {
        $u = $this->user();

        return $u instanceof Usuario && in_array((int) $u->id_rol, self::ROLES_CLIENTE, true);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ids' => ['sometimes', 'array'],
            'ids.*' => ['integer', 'min:1'],
            'todas' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            if ($this->boolean('todas')) {
                return;
            }
            $ids = $this->input('ids', []);
            if (is_array($ids) && $ids !== []) {
                return;
            }
            $v->errors()->add('ids', 'Seleccione al menos una notificación.');
        });
    }
}
