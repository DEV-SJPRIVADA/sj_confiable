<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\DocumentoRespuesta;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SolicitudDocumentoController extends Controller
{
    public function eliminarDocumentoSolicitud(Solicitud $solicitud, int $documento): RedirectResponse
    {
        $this->authorize('deleteAdjuntoExpedienteAsConsultor', $solicitud);

        $doc = Documento::query()
            ->where('solicitud_id', $solicitud->id)
            ->whereKey($documento)
            ->firstOrFail();

        DB::transaction(function () use ($doc): void {
            $this->borrarArchivoPublico((string) ($doc->ruta_documento ?? ''));
            $doc->delete();
        });

        return redirect()
            ->route('panel.consultor.solicitudes.show', $solicitud)
            ->with('status', 'Documento eliminado.')
            ->withFragment('consultor-main-documentos');
    }

    public function eliminarDocumentoRespuesta(Solicitud $solicitud, int $documentoRespuesta): RedirectResponse
    {
        $this->authorize('deleteDocumentoRespuestaOperativaAsConsultor', $solicitud);

        $dr = DocumentoRespuesta::query()
            ->whereKey($documentoRespuesta)
            ->whereHas('respuestaMadre', fn ($q) => $q->where('solicitud_id', $solicitud->id))
            ->firstOrFail();

        DB::transaction(function () use ($dr): void {
            $this->borrarArchivoPublico((string) ($dr->ruta_documentoResp ?? ''));
            $dr->delete();
        });

        return redirect()
            ->route('panel.consultor.solicitudes.show', $solicitud)
            ->with('status', 'Documento operativo eliminado.')
            ->withFragment('consultor-main-documentos');
    }

    private function borrarArchivoPublico(string $rutaRelativa): void
    {
        $r = ltrim(str_replace('\\', '/', $rutaRelativa), '/');
        if ($r === '') {
            return;
        }
        if (! str_starts_with($r, 'uploads/')) {
            $r = 'uploads/'.$r;
        }
        if (Storage::disk('public')->exists($r)) {
            Storage::disk('public')->delete($r);
        }
    }
}
