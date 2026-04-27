<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Models\Usuario;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUsuarioGestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function failedValidation(Validator $validator): void
    {
        $input = $this->except('password');
        /** @var Usuario $usuario */
        $usuario = $this->route('usuario');
        $input['editing_user_id'] = $usuario->id_usuario;

        throw new HttpResponseException(
            redirect()
                ->route('panel.consultor.usuarios.index', array_merge(
                    $this->queryForRedirectBack(),
                    [
                        'open_modal' => 'editar',
                        'edit_usuario' => $usuario->id_usuario,
                    ],
                ))
                ->withInput($input)
                ->with('open_modal', 'editar')
                ->with('edit_usuario_id', $usuario->id_usuario)
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
            static fn (mixed $v): bool => $v !== null && $v !== ''
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->route('usuario')?->id_usuario;

        return [
            'paterno' => ['required', 'string', 'max:245'],
            'materno' => ['nullable', 'string', 'max:245'],
            'nombre' => ['required', 'string', 'max:245'],
            'telefono' => ['required', 'string', 'max:15'],
            'celular' => ['required', 'string', 'max:15'],
            'correo' => ['required', 'email', 'max:245'],
            'direccion' => ['nullable', 'string', 'max:100'],
            'identificacion' => ['required', 'string', 'max:50'],
            'usuario' => ['required', 'string', 'max:245', Rule::unique('t_usuarios', 'usuario')->ignore($id, 'id_usuario')],
            'password' => ['nullable', 'string', Password::min(8)->max(15)->mixedCase()->numbers()->symbols()],
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
