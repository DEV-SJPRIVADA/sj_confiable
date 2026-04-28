<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteSolicitudDocumentoRequest;
use App\Models\Documento;
use App\Models\DocumentoRespuesta;
use App\Models\Solicitud;
use App\Services\Solicitud\ClienteSolicitudDocumentoAdjuntoService;
use App\Services\Solicitud\SolicitudDocumentoPathResolver;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SolicitudArchivoController extends Controller
{
    public function descargarDocumentoSolicitud(
        Solicitud $solicitud,
        int $documento,
        SolicitudDocumentoPathResolver $resolver,
    ): BinaryFileResponse {
        $this->authorize('view', $solicitud);

        $doc = Documento::query()
            ->where('solicitud_id', $solicitud->id)
            ->whereKey($documento)
            ->firstOrFail();

        $absolute = $resolver->resolve($doc->ruta_documento);
        if ($absolute === null) {
            abort(404, 'Archivo no encontrado en el servidor.');
        }

        $nombre = $this->safeDownloadName($doc->nombre_documento, $absolute);

        return response()->download($absolute, $nombre);
    }

    public function descargarDocumentoRespuesta(
        Solicitud $solicitud,
        int $documentoRespuesta,
        SolicitudDocumentoPathResolver $resolver,
    ): BinaryFileResponse {
        $this->authorize('view', $solicitud);

        $dr = DocumentoRespuesta::query()
            ->whereKey($documentoRespuesta)
            ->whereHas('respuestaMadre', fn ($q) => $q->where('solicitud_id', $solicitud->id))
            ->firstOrFail();

        $absolute = $resolver->resolve($dr->ruta_documentoResp);
        if ($absolute === null) {
            abort(404, 'Archivo no encontrado en el servidor.');
        }

        $nombre = $this->safeDownloadName($dr->nombre_documentoResp, $absolute);

        return response()->download($absolute, $nombre);
    }

    public function almacenarDocumentoCliente(
        StoreClienteSolicitudDocumentoRequest $request,
        Solicitud $solicitud,
        ClienteSolicitudDocumentoAdjuntoService $adjuntar,
    ): RedirectResponse {
        $this->authorize('attachDocumentosCliente', $solicitud);

        $adjuntar->adjuntar($solicitud, $request->file('documento'));

        return redirect()
            ->route('panel.cliente.solicitudes.show', $solicitud)
            ->with('status', 'Documento adjuntado correctamente.');
    }

    private function safeDownloadName(string $preferred, string $fallbackAbsolute): string
    {
        $base = basename($preferred);
        if ($base !== '' && $base !== '.' && $base !== '..') {
            return $base;
        }

        return basename($fallbackAbsolute);
    }
}
