<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Solicitud;

class UpdateClienteSolicitudRequest extends StoreClienteSolicitudRequest
{
    public function authorize(): bool
    {
        $solicitud = $this->route('solicitud');

        return $solicitud instanceof Solicitud
            && $this->user() !== null
            && $this->user()->can('update', $solicitud);
    }
}
