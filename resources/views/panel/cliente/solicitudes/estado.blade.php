@extends('layouts.app')

@php
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
@endphp

@section('title', 'Estado de solicitud #'.$solicitud->id)

@push('styles')
<style>
    /* Alineación con legado SJ: tabla de datos + bloc de historial con cabecera azul */
    .cli-solic-estado-page {
        background: #f1f3f5;
        margin-inline: calc(-1 * var(--bs-gutter-x, 1rem));
        padding-inline: var(--bs-gutter-x, 1rem);
        padding-block: 0.75rem;
    }
    .cli-solic-estado-header { border-bottom: 1px solid #dee2e6; }
    .cli-solic-estado-card {
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        box-shadow: none !important;
        background: #fff;
    }
    .cli-solic-estado-card .card-header--detalle {
        background: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6 !important;
        font-weight: 600;
        font-size: 0.95rem;
        color: #212529;
    }
    /* Tabla tipo detalle viejo (etiquetas en columna izquierda) */
    .cli-solic-detalle-table tbody th.cli-solic-detalle-label {
        width: 36%;
        min-width: 7rem;
        background: #f8f9fa !important;
        font-weight: 600;
        font-size: 0.8125rem;
        color: #495057;
        vertical-align: top !important;
        border-color: #dee2e6 !important;
    }
    .cli-solic-detalle-table tbody td {
        font-size: 0.8125rem;
        color: #212529;
        vertical-align: top !important;
        white-space: normal !important;
        word-break: break-word;
        line-height: 1.45;
        border-color: #dee2e6 !important;
    }
    /* Historial: cabecera barra azul tipo legado */
    .cli-solic-historial-head {
        background: #0d6efd !important;
        color: #fff !important;
        font-weight: 600 !important;
        font-size: 0.9375rem;
        letter-spacing: 0.01em;
        border: none !important;
        border-radius: 0 !important;
    }
    .cli-solic-historial-body {
        background: #fff;
        padding: 1rem 1rem 1.1rem !important;
    }
    .cli-solic-timeline-entry {
        position: relative;
        padding: 0 0 1.15rem 1rem;
        margin: 0 0 1.05rem 0;
        border-bottom: 1px solid #e9ecef;
        border-left: 4px solid #0d6efd;
    }
    .cli-solic-timeline-entry:last-child {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
        border-bottom: none;
    }
    .cli-solic-timeline-user {
        font-weight: 600;
        font-size: 0.875rem;
        color: #0d6efd;
    }
    .cli-solic-timeline-fecha {
        font-size: 0.875rem;
        color: #6c757d;
        white-space: nowrap;
    }
    .cli-solic-timeline-texto {
        font-size: 0.875rem;
        color: #333;
        margin-bottom: 0.35rem !important;
        line-height: 1.5;
    }
    .cli-solic-timeline-estado {
        font-size: 0.8125rem;
        color: #6c757d;
        margin-bottom: 0 !important;
    }
    .cli-solic-doc-row { border-bottom: 1px solid #eee; }
    .cli-solic-doc-row:last-of-type { border-bottom: none; }
    .cli-solic-doc-dl {
        width: 2.35rem;
        height: 2.35rem;
        padding: 0;
        border-radius: 50%;
        flex-shrink: 0;
        line-height: 1;
    }
    .cli-solic-doc-dl i { font-size: 0.95rem; color: #fff; }
    .cli-solic-doc-tipo { font-size: 0.75rem; }
    .cli-solic-title-line { font-size: 1.25rem; }
</style>
@endpush

@section('content')
<div class="cli-solic-estado-page">
<div class="cli-solic-estado-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <h1 class="cli-solic-title-line mb-0 fw-bold text-body d-flex flex-wrap align-items-center gap-2">
        <span>Estado de Solicitud #{{ $solicitud->id }}</span>
        <span class="badge rounded-pill {{ $badgeEstado }}">{{ $solicitud->estado }}</span>
    </h1>
    @php
        $prev = url()->previous();
        $volverUrl = ($prev !== url()->current() && is_string($prev) && str_starts_with($prev, (string) config('app.url')))
            ? $prev
            : route('panel.cliente.solicitudes.index');
    @endphp
    <a href="{{ $volverUrl }}"
       class="btn btn-outline-secondary btn-sm text-uppercase px-3 fw-semibold">
        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Volver
    </a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 cli-solic-estado-card mb-3">
            <div class="card-header border-0 card-header--detalle d-flex align-items-center gap-2 py-2 px-3">
                <i class="fas fa-info-circle text-primary" aria-hidden="true"></i>
                Detalles de la Solicitud
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-legacy table-sm table-bordered mb-0 cli-solic-detalle-table">
                        <tbody>
                            <tr>
                                <th scope="row" class="cli-solic-detalle-label">Servicio</th>
                                <td>{{ $servicioEtiqueta }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cli-solic-detalle-label">Ciudad</th>
                                <td>{{ $solicitud->ciudad_solicitud_servicio ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cli-solic-detalle-label">Fecha</th>
                                <td>{{ $solicitud->fecha_creacion?->format('d/m/Y h:i A') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="cli-solic-detalle-label">Analista</th>
                                <td>{{ $solicitud->creador?->usuario ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="documentos" data-section="cli-estado-documentos" class="card border-0 cli-solic-estado-card">
            <div class="card-header border-0 card-header--detalle d-flex align-items-center gap-2 py-2 px-3">
                <i class="fas fa-file-alt text-primary" aria-hidden="true"></i>
                Documentos Relacionados
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
                                    <div class="cli-solic-doc-tipo text-danger">Documento Solicitud</div>
                                </div>
                            </div>
                            <a href="{{ route('panel.cliente.solicitudes.archivos.documento', ['solicitud' => $solicitud->id, 'documento' => $d->id]) }}"
                               class="btn btn-primary cli-solic-doc-dl flex-shrink-0 d-inline-flex align-items-center justify-content-center"
                               title="Descargar"
                               aria-label="Descargar {{ $d->nombre_documento }}">
                                <i class="fas fa-download" aria-hidden="true"></i>
                            </a>
                        </div>
                    @endforeach
                    @foreach ($solicitud->respuestasMadre as $rm)
                        @foreach ($rm->documentos as $dr)
                            <div class="cli-solic-doc-row px-3 py-2 d-flex align-items-start justify-content-between gap-2">
                                <div class="d-flex gap-2 min-w-0">
                                    <i class="fas fa-file-pdf text-danger mt-1 flex-shrink-0" aria-hidden="true"></i>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-break small">{{ $dr->nombre_documentoResp }}</div>
                                        <div class="cli-solic-doc-tipo text-danger">Documento Respuesta</div>
                                    </div>
                                </div>
                                <a href="{{ route('panel.cliente.solicitudes.archivos.respuesta', ['solicitud' => $solicitud->id, 'documentoRespuesta' => $dr->id]) }}"
                                   class="btn btn-primary cli-solic-doc-dl flex-shrink-0 d-inline-flex align-items-center justify-content-center"
                                   title="Descargar"
                                   aria-label="Descargar {{ $dr->nombre_documentoResp }}">
                                    <i class="fas fa-download" aria-hidden="true"></i>
                                </a>
                            </div>
                        @endforeach
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 cli-solic-estado-card overflow-hidden h-100">
            <div class="card-header cli-solic-historial-head py-2 px-3 d-flex align-items-center gap-2">
                <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
                Historial de Respuestas
            </div>
            <div class="card-body cli-solic-historial-body border-top-0">
                @if ($solicitud->historialRespuestas->isEmpty())
                    <p class="text-muted small mb-0">Sin movimientos en el historial.</p>
                @else
                    @foreach ($solicitud->historialRespuestas as $h)
                        <div class="cli-solic-timeline-entry">
                            <div class="d-flex flex-wrap justify-content-between gap-2 align-items-baseline mb-1">
                                <span class="cli-solic-timeline-user">{{ $h->usuario?->usuario ?? '—' }}</span>
                                <span class="cli-solic-timeline-fecha">{{ $h->fecha_respuesta?->format('d/m/Y h:i A') ?? '—' }}</span>
                            </div>
                            <p class="cli-solic-timeline-texto">{!! nl2br(e($h->respuesta)) !!}</p>
                            @if ($h->estado_anterior || $h->estado_actual)
                                <p class="cli-solic-timeline-estado mb-0">
                                    Estado: {{ $h->estado_anterior ?? '—' }} -> {{ $h->estado_actual ?? '—' }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection
