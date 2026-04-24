<?php

declare(strict_types=1);

namespace App\Services\Catalog;

use App\Models\SolicitudUsuario;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

final class SolicitudUsuarioRespuestaService
{
    public function responder(SolicitudUsuario $solicitud, Usuario $responde, string $estado, string $comentario): void
    {
        if (! in_array($estado, ['Aprobada', 'Rechazada'], true)) {
            throw new \InvalidArgumentException('Estado de respuesta inválido.');
        }
        if ($solicitud->estado !== 'Pendiente') {
            throw new \DomainException('La solicitud ya fue respondida.');
        }

        DB::transaction(function () use ($solicitud, $responde, $estado, $comentario) {
            $solicitud->update([
                'estado' => $estado,
                'comentario_respuesta' => $comentario,
                'id_usuario_responde' => $responde->id_usuario,
                'fecha_respuesta' => now(),
            ]);
        });
    }
}
