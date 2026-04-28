@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    $servicioEtiqueta = trim($solicitud->labelServiciosContratados());
    if ($servicioEtiqueta === '' || $servicioEtiqueta === '—') {
        $servicioEtiqueta = 'No encontrado';
    }
    $estLower = mb_strtolower(trim((string) $solicitud->estado));
    $badgeEstado = 'bg-secondary';
    if (str_contains($estLower, 'proceso')) {
        $badgeEstado = 'bg-primary';
    } elseif (str_contains($estLower, 'complet')) {
        $badgeEstado = 'bg-success';
    } elseif (str_contains($estLower, 'cancel')) {
        $badgeEstado = 'bg-danger';
    } elseif (str_contains($estLower, 'registr')) {
        $badgeEstado = 'bg-info text-dark';
    }
    $urlArchivo = static function (string $rutaRelativa): ?string {
        $r = ltrim(str_replace('\\', '/', $rutaRelativa), '/');
        if ($r === '') {
            return null;
        }
        $checks = [
            [public_path($r), asset($r)],
            [public_path('uploads/'.$r), asset('uploads/'.$r)],
            [public_path('documentos/'.$r), asset('documentos/'.$r)],
        ];
        foreach ($checks as [$abs, $url]) {
            if (is_string($abs) && is_file($abs)) {
                return $url;
            }
        }
        if (Storage::disk('public')->exists($r)) {
            return Storage::disk('public')->url($r);
        }

        return null;
    };
@endphp

@section('title', 'Estado de solicitud #'.$solicitud->id)

@push('styles')
<style>
    .cli-solic-estado-header { border-bottom: 1px solid #dee2e6; }
    .cli-solic-estado-card .card-header { background: #f8f9fa; font-weight: 600; font-size: 0.95rem; }
    .cli-solic-doc-row { border-bottom: 1px solid #eee; }
    .cli-solic-doc-row:last-child { border-bottom: none; }
    .cli-solic-timeline-item { border-left: 4px solid #0d6efd; padding-left: 1rem; margin-bottom: 1.25rem; }
    .cli-solic-timeline-head { background: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="cli-solic-estado-header d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-2 fw-bold text-body">Estado de Solicitud #{{ $solicitud->id }}</h1>
        <p class="mb-0 small">
            <span class="text-muted">Estado actual:</span>
            <span class="badge rounded-pill {{ $badgeEstado }}">{{ $solicitud->estado }}</span>
        </p>
    </div>
    @php
        $prev = url()->previous();
        $volverUrl = ($prev !== url()->current() && is_string($prev) && str_starts_with($prev, (string) config('app.url')))
            ? $prev
            : route('panel.cliente.solicitudes.index');
    @endphp
    <a href="{{ $volverUrl }}"
       class="btn btn-outline-secondary btn-sm text-uppercase px-3">
        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Volver
    </a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm cli-solic-estado-card mb-3">
            <div class="card-header border-0 d-flex align-items-center gap-2">
                <i class="fas fa-info-circle text-primary" aria-hidden="true"></i>
                Detalles de la Solicitud
            </div>
            <div class="card-body small">
                <p class="mb-2"><span class="fw-semibold">Servicio:</span><br>{{ $servicioEtiqueta }}</p>
                <p class="mb-2"><span class="fw-semibold">Ciudad:</span><br>{{ $solicitud->ciudad_solicitud_servicio ?? '—' }}</p>
                <p class="mb-2"><span class="fw-semibold">Fecha:</span><br>{{ $solicitud->fecha_creacion?->format('d/m/Y h:i A') ?? '—' }}</p>
                <p class="mb-0"><span class="fw-semibold">Analista:</span><br>{{ $solicitud->creador?->usuario ?? '—' }}</p>
            </div>
        </div>

        <div id="documentos" class="card border-0 shadow-sm cli-solic-estado-card">
            <div class="card-header border-0 d-flex align-items-center gap-2">
                <i class="fas fa-file-alt text-primary" aria-hidden="true"></i>
                Documentos relacionados
            </div>
            <div class="card-body p-0">
                @php
                    $tieneDocs = $solicitud->documentos->isNotEmpty()
                        || $solicitud->respuestasMadre->contains(fn ($rm) => $rm->documentos->isNotEmpty());
                @endphp
                @if (! $tieneDocs)
                    <p class="p-3 mb-0 text-muted small">Sin documentos asociados.</p>
                @else
                    @foreach ($solicitud->documentos as $d)
                        <div class="cli-solic-doc-row px-3 py-2 d-flex align-items-start justify-content-between gap-2">
                            <div class="d-flex gap-2 min-w-0">
                                <i class="fas fa-file-pdf text-danger mt-1 flex-shrink-0" aria-hidden="true"></i>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-break small">{{ $d->nombre_documento }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Documento solicitud</div>
                                </div>
                            </div>
                            @php $u = $urlArchivo($d->ruta_documento); @endphp
                            @if ($u)
                                <a href="{{ $u }}" class="btn btn-sm btn-outline-primary rounded-circle flex-shrink-0" download title="Descargar" target="_blank" rel="noopener"><i class="fas fa-download" aria-hidden="true"></i></a>
                            @else
                                <span class="text-muted small" title="Archivo no disponible en el servidor"><i class="fas fa-ban"></i></span>
                            @endif
                        </div>
                    @endforeach
                    @foreach ($solicitud->respuestasMadre as $rm)
                        @foreach ($rm->documentos as $dr)
                            <div class="cli-solic-doc-row px-3 py-2 d-flex align-items-start justify-content-between gap-2">
                                <div class="d-flex gap-2 min-w-0">
                                    <i class="fas fa-file-pdf text-danger mt-1 flex-shrink-0" aria-hidden="true"></i>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-break small">{{ $dr->nombre_documentoResp }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Documento respuesta</div>
                                    </div>
                                </div>
                                @php $u = $urlArchivo($dr->ruta_documentoResp); @endphp
                                @if ($u)
                                    <a href="{{ $u }}" class="btn btn-sm btn-outline-primary rounded-circle flex-shrink-0" download title="Descargar" target="_blank" rel="noopener"><i class="fas fa-download" aria-hidden="true"></i></a>
                                @else
                                    <span class="text-muted small" title="Coloque el archivo en public/ o storage/app/public según la ruta del legado"><i class="fas fa-ban"></i></span>
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header cli-solic-timeline-head border-0 py-2 px-3 d-flex align-items-center gap-2">
                <i class="fas fa-history" aria-hidden="true"></i>
                Historial de respuestas
            </div>
            <div class="card-body">
                @if ($solicitud->historialRespuestas->isEmpty())
                    <p class="text-muted small mb-0">Sin movimientos en el historial.</p>
                @else
                    @foreach ($solicitud->historialRespuestas as $h)
                        <div class="cli-solic-timeline-item">
                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-1">
                                <span class="fw-semibold text-primary">{{ $h->usuario?->usuario ?? '—' }}</span>
                                <span class="text-muted small">{{ $h->fecha_respuesta?->format('d/m/Y h:i A') ?? '—' }}</span>
                            </div>
                            <p class="small mb-1">{!! nl2br(e($h->respuesta)) !!}</p>
                            @if ($h->estado_anterior || $h->estado_actual)
                                <p class="small text-muted mb-0">
                                    Estado: {{ $h->estado_anterior ?? '—' }} <i class="fas fa-arrow-right mx-1" aria-hidden="true"></i> {{ $h->estado_actual ?? '—' }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
