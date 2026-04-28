<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Models\Solicitud;
use Illuminate\Support\Facades\DB;

final class ClienteSolicitudCancelacionService
{
    public function cancelar(Solicitud $solicitud): void
    {
        DB::transaction(function () use ($solicitud): void {
            $solicitud->estado = 'Cancelado';
            $solicitud->activo = false;
            $solicitud->save();
        });
    }
}
