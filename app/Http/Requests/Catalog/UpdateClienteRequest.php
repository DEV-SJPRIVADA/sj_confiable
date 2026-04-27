<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Models\Cliente;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function failedValidation(Validator $validator): void
    {
        $input = $this->all();
        /** @var Cliente $cliente */
        $cliente = $this->route('cliente');
        $input['editing_cliente_id'] = $cliente->id_cliente;

        throw new HttpResponseException(
            redirect()
                ->route('panel.consultor.clientes.index', array_merge(
                    $this->queryForRedirectBack(),
                    [
                        'open_modal' => 'editar',
                        'edit_cliente' => $cliente->id_cliente,
                    ],
                ))
                ->withInput($input)
                ->with('open_modal', 'editar')
                ->with('edit_cliente_id', $cliente->id_cliente)
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
            'nit' => ['required', 'integer'],
            'razon_social' => ['required', 'string', 'max:255'],
            'direccion_cliente' => ['nullable', 'string', 'max:255'],
            'ciudad_cliente' => ['nullable', 'string', 'max:50'],
            'telefono_cliente' => ['nullable', 'string', 'max:50'],
            'correo_cliente' => ['nullable', 'email', 'max:255'],
            'nombre' => ['nullable', 'string', 'max:100'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'tipo_cliente' => ['required', 'string', 'max:100'],
        ];
    }
}
