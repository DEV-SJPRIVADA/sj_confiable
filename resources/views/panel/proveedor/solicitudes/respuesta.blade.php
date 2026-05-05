@extends('layouts.app')

@php
    use App\Http\Requests\ResponderSolicitudProveedorRequest;
    use Illuminate\Support\Facades\Storage;

    /** @var bool $puedeResponder */
    /** @var string $asignadoPorEtiqueta */
    $madre = $solicitud->ultimaRespuestaMadre;
    $estadosProveedorList = ResponderSolicitudProveedorRequest::ESTADOS_PERMITIDOS;
    $stPrv = (string) ($solicitud->estado ?? '');
    if ($stPrv !== '' && ! in_array($stPrv, $estadosProveedorList, true)) {
        $estadosProveedorList = array_values(array_unique(array_merge([$stPrv], $estadosProveedorList)));
    }
    $urlPdfOperativo = static function (string $ruta): string {
        $r = ltrim($ruta, '/');
        if (str_starts_with($r, 'uploads/')) {
            return Storage::disk('public')->url($r);
        }

        return Storage::disk('public')->url('uploads/'.$r);
    };
@endphp

@section('title', 'Solicitud #'.$solicitud->id)

@push('styles')
<style>
    .prov-resp-page {
        background: #e9ecef;
        margin-left: -0.75rem;
        margin-right: -0.75rem;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-bottom: 2rem;
        min-height: calc(100vh - 6rem);
    }
    .prov-resp-toolbar .btn {
        min-width: 7.5rem;
    }
    .prov-resp-asignacion {
        background: #e7f5fb;
        border: 1px solid #b8dce9;
        border-radius: 0.35rem;
        padding: 1rem 1.15rem;
    }
    .prov-resp-pdf-frame {
        width: 100%;
        min-height: 32rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background: #f8f9fa;
    }
    .solicitud-modal-detalle__content { border-radius: 0.45rem; overflow: hidden; }
    .solicitud-modal-detalle__bar {
        background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%) !important;
    }
    .solicitud-modal-detalle__archivos {
        background: #e9ecef;
        color: #212529;
        border: 1px solid #dee2e6;
    }
    .solicitud-modal-detalle__asoc {
        background: #fff;
        border-color: #dee2e6 !important;
    }
    .solicitud-modal-detalle__asoc-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.1rem;
        height: 2.1rem;
        border-radius: 50%;
        background: #ffc107;
        flex-shrink: 0;
        font-size: 1rem;
    }
    .prov-resp-oc-detalle .offcanvas-body .solicitud-anchor-documentos {
        box-shadow: none !important;
    }
    .prov-resp-hist-body .prov-resp-hist-item:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
@endpush

@section('content')
<div class="prov-resp-page">
    <p class="mb-2 pt-2">
        <a href="{{ route('panel.proveedor.solicitudes.index') }}" class="d-inline-flex align-items-center text-decoration-none link-primary fw-medium small">
            <i class="fas fa-arrow-left me-2" aria-hidden="true"></i>Volver al listado
        </a>
    </p>
    <div class="prov-resp-toolbar d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#provRespOcDetalle" aria-controls="provRespOcDetalle">
            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>DETALLE
        </button>
        <h1 class="h5 mb-0 text-primary fw-semibold text-center flex-grow-1 px-2">Solicitud #{{ $solicitud->id }}</h1>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#provRespOcHistorial" aria-controls="provRespOcHistorial">
            <i class="fas fa-clock me-1" aria-hidden="true"></i>HISTORIAL
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Nueva respuesta</div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger small" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (! $puedeResponder)
                <p class="text-muted small mb-3">La solicitud está cerrada o completada; sólo puede consultar la información registrada.</p>
            @endif

            <form method="post" action="{{ route('panel.proveedor.solicitudes.respuesta.store', $solicitud) }}" enctype="multipart/form-data" @if(! $puedeResponder) class="opacity-50" onsubmit="return false;" @endif>
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-medium" for="nueva_respuesta">Respuesta</label>
                    <textarea id="nueva_respuesta" name="nueva_respuesta" rows="5" class="form-control @if(! $puedeResponder) bg-light @endif"
                        @if ($puedeResponder) required minlength="3" maxlength="20000" @else disabled @endif>{{ old('nueva_respuesta', $madre?->respuesta ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium" for="documentos_gestion">Documentos adjuntos (Máx. 10 PDFs)</label>
                    <input type="file" id="documentos_gestion" name="documentos[]" class="form-control @if(! $puedeResponder) bg-light @endif" accept=".pdf,application/pdf" multiple
                        @if ($puedeResponder) @else disabled @endif>
                    @if ($puedeResponder)
                        <p class="small text-muted mb-0 mt-1" id="provRespDocCount" aria-live="polite">Archivos seleccionados: 0 / 10</p>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium" for="nuevo_estado">Nuevo estado</label>
                    <select id="nuevo_estado" name="nuevo_estado" class="form-select @if(! $puedeResponder) bg-light @endif" @if ($puedeResponder) required @else disabled @endif>
                        @foreach ($estadosProveedorList as $est)
                            <option value="{{ $est }}" @selected(old('nuevo_estado', $solicitud->estado) === $est)>{{ $est }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($puedeResponder)
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5 text-uppercase fw-semibold">Enviar respuesta</button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="prov-resp-asignacion mb-3">
        <div class="fw-semibold text-primary mb-2">Asignación de asociado de negocio</div>
        <div class="row small g-2">
            <div class="col-md-6">
                <span class="text-muted">Asociado asignado</span><br>
                {{ $solicitud->proveedorAsignado?->nombre_comercial ?? $solicitud->proveedorAsignado?->razon_social_proveedor ?? '—' }}
            </div>
            <div class="col-md-6">
                <span class="text-muted">NIT</span><br>
                {{ $solicitud->proveedorAsignado?->NIT_proveedor ?? '—' }}
            </div>
            <div class="col-md-6">
                <span class="text-muted">Correo (contacto)</span><br>
                {{ $solicitud->proveedorAsignado?->correo_proveedor ?? auth()->user()?->persona?->correo ?? auth()->user()?->usuario ?? '—' }}
            </div>
            <div class="col-md-6">
                <span class="text-muted">Usuario en sesión</span><br>
                {{ auth()->user()?->usuario ?? '—' }}
            </div>
            <div class="col-md-6">
                <span class="text-muted">Fecha de asignación</span><br>
                {{ $solicitud->fecha_asignacion_proveedor?->format('d/m/Y h:i A') ?? '—' }}
            </div>
            <div class="col-md-6">
                <span class="text-muted">Asignado por</span><br>
                {{ $asignadoPorEtiqueta }}
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Documentos adjuntos (respuesta principal)</div>
        <div class="card-body">
            @if ($madre === null)
                <p class="text-muted small mb-0">Sin respuesta operativa registrada aún.</p>
            @elseif ($madre->documentos->isEmpty())
                <p class="text-muted small mb-0">Sin archivos adjuntos en la última respuesta operativa.</p>
            @else
                @foreach ($madre->documentos as $doc)
                    <div class="mb-4 @if(!$loop->last) pb-4 border-bottom @endif">
                        <div class="small fw-medium mb-2">{{ $doc->nombre_documentoResp }}</div>
                        <iframe class="prov-resp-pdf-frame mb-2" title="Vista previa {{ $doc->nombre_documentoResp }}" src="{{ $urlPdfOperativo((string) $doc->ruta_documentoResp) }}#toolbar=1"></iframe>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ $urlPdfOperativo((string) $doc->ruta_documentoResp) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">Abrir en nueva pestaña</a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    @include('panel.proveedor.solicitudes._offcanvases-respuesta-solicitud', ['solicitud' => $solicitud])
</div>
@endsection

@push('scripts')
@if ($puedeResponder)
<script>
(function () {
    var inp = document.getElementById('documentos_gestion');
    var out = document.getElementById('provRespDocCount');
    if (!inp || !out) return;
    function refresh() {
        var n = inp.files ? inp.files.length : 0;
        out.textContent = 'Archivos seleccionados: ' + Math.min(n, 10) + ' / 10';
    }
    inp.addEventListener('change', refresh);
    refresh();
})();
</script>
@endif
<script>
(function () {
    document.querySelectorAll('[data-prov-resp-oc-scroll-doc]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var t = document.getElementById('provRespOc-docs-anchor');
            if (t) {
                t.scrollIntoView({ block: 'start', behavior: 'smooth' });
            }
        });
    });
})();
</script>
@endpush
