<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Models\Documento;
use App\Models\Solicitud;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ClienteSolicitudDocumentoAdjuntoService
{
    public function adjuntar(
        Solicitud $solicitud,
        UploadedFile $file,
        bool $visibleParaCliente = true,
        bool $cargadoDesdePanelCliente = false,
    ): Documento {
        return DB::transaction(function () use ($solicitud, $file, $visibleParaCliente, $cargadoDesdePanelCliente): Documento {
            $original = $file->getClientOriginalName();
            $stored = $file->storeAs(
                'uploads',
                'cli_'.Str::random(12).'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $original),
                'public',
            );

            $doc = new Documento;
            $doc->solicitud_id = $solicitud->id;
            $doc->nombre_documento = $original !== '' ? $original : basename($stored);
            $doc->ruta_documento = $stored;
            $doc->visible_para_cliente = $visibleParaCliente;
            $doc->cargado_desde_panel_cliente = $cargadoDesdePanelCliente;
            $doc->save();

            return $doc;
        });
    }
}
