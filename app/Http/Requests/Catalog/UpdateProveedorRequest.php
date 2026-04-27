<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Models\Proveedor;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function failedValidation(Validator $validator): void
    {
        $input = $this->all();
        /** @var Proveedor $proveedor */
        $proveedor = $this->route('proveedor');
        $input['editing_proveedor_id'] = $proveedor->id_proveedor;

        throw new HttpResponseException(
            redirect()
                ->route('panel.consultor.asociados.index', array_merge(
                    $this->queryForRedirectBack(),
                    [
                        'open_modal' => 'editar',
                        'edit_proveedor' => $proveedor->id_proveedor,
                    ],
                ))
                ->withInput($input)
                ->with('open_modal', 'editar')
                ->with('edit_proveedor_id', $proveedor->id_proveedor)
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
        return (new StoreProveedorRequest)->rules();
    }
}
