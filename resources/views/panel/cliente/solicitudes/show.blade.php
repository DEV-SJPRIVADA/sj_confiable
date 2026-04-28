@extends('layouts.app')

@php
    $tipoDocs = [
        'CC' => 'Cédula de ciudadanía',
        'CE' => 'Cédula de extranjería',
        'TI' => 'Tarjeta de identidad',
        'PA' => 'Pasaporte',
        'NIT' => 'NIT',
    ];
    $tipoDocEtiqueta = $tipoDocs[trim((string) $solicitud->tipo_identificacion)] ?? (trim((string) $solicitud->tipo_identificacion) ?: '—');
    $nombrePaquete = trim($solicitud->labelServiciosContratados());
@endphp

@section('title', 'Detalle de solicitud #'.$solicitud->id)

@push('styles')
<style>
    /* Full bleed hero (similar al sistema legado SJ) */
    .cli-detalle-fw { width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); position: relative; }
    .cli-detalle-completo-hero {
        position: relative;
        overflow: hidden;
        background-size: cover;
        background-position: center center;
        min-height: 11.5rem;
    }
    @media (min-width: 768px) {
        .cli-detalle-completo-hero { min-height: 13.5rem; }
    }
    .cli-detalle-completo-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(9, 32, 66, 0.88) 0%, rgba(12, 45, 90, 0.58) 100%);
        z-index: 0;
    }
    .cli-detalle-completo-hero-inner { position: relative; z-index: 1; }
    .cli-detalle-card {
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.065) !important;
        background: #fff;
    }
    .cli-detalle-grid dt { font-size: 0.8125rem; color: #6c757d; font-weight: 500; }
    .cli-detalle-grid dd { font-size: 0.8125rem; margin-bottom: 0.85rem !important; word-break: break-word; color: #212529; }
    .cli-detalle-grid dd:last-of-type { margin-bottom: 0 !important; }
    .cli-paquete-strip {
        background: #e8edf2;
        border: 1px solid #cfd8dc;
        border-radius: 0.4rem;
        font-size: 0.8125rem;
        color: #052c65;
        align-items: center;
    }
    .cli-paquete-badge-legado {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.28rem 0.55rem;
        border-radius: 0.35rem !important;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1.2;
    }
    .cli-paquete-badge-legado i { font-size: 0.85rem; }
    /* Botón cuadrado azul tipo legado SJ (lápiz sobre papel) */
    .cli-btn-edit-legado {
        width: 2.25rem;
        height: 2.25rem;
        padding: 0;
        margin: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.4rem !important;
        flex-shrink: 0;
        background: linear-gradient(180deg, #0d6efd 0%, #0b5ed7 100%);
        box-shadow:
            1px 2px 0 rgba(0, 0, 0, 0.06),
            2px 3px 6px rgba(13, 110, 253, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.35);
        line-height: 1;
        transition: filter 0.15s ease, transform 0.1s ease;
    }
    a.cli-btn-edit-legado-link:hover .cli-btn-edit-legado {
        filter: brightness(1.06);
        transform: translateY(-1px);
    }
    a.cli-btn-edit-legado-link:focus-visible .cli-btn-edit-legado {
        outline: 2px solid #0dcaf0;
        outline-offset: 2px;
    }
    .cli-btn-edit-legado i.fa-edit {
        color: #fff;
        font-size: 1.05rem;
    }
    .cli-archivo-acc {
        width: 2rem;
        height: 2rem;
        padding: 0;
        border-radius: 0.25rem !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    @media (min-width: 768px) {
        .cli-detalle-col-sep { border-right: 1px solid #dee2e6 !important; }
    }
    /* Modal subir PDF — paridad legado SJ */
    #modalSubirDocumentoPdf .cli-modal-pdf-header {
        background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%);
        color: #fff;
    }
    #modalSubirDocumentoPdf .cli-modal-pdf-header .btn-close { filter: invert(1); opacity: 0.9; }
    #modalSubirDocumentoPdf .cli-modal-pdf-footer {
        background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%);
    }
    #modalSubirDocumentoPdf .modal-footer .btn-secondary {
        background-color: #495057;
        border-color: #495057;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    var modalEl = document.getElementById('modalSubirDocumentoPdf');
    if (!modalEl || typeof bootstrap === 'undefined') {
        return;
    }
    var fileInput = document.getElementById('modal_adjunto_pdf');
    modalEl.addEventListener('hidden.bs.modal', function () {
        if (fileInput) {
            fileInput.value = '';
        }
        var form = modalEl.querySelector('form');
        if (form) {
            form.reset();
        }
    });
})();
@if ($errors->has('documento'))
(function () {
    var el = document.getElementById('modalSubirDocumentoPdf');
    if (!el || typeof bootstrap === 'undefined') return;
    var m = new bootstrap.Modal(el);
    m.show();
})();
@endif
</script>
@endpush

@section('content')
<div class="pb-5">
    {{-- Hero estilo viejo SJ --}}
    <div class="cli-detalle-fw mb-4">
        <div class="cli-detalle-completo-hero text-white text-center d-flex align-items-center"
             style="background-image: url('{{ asset('images/fondo_app_tecnologia.png') }}');">
            <div class="container-fluid py-5 px-3 cli-detalle-completo-hero-inner">
                <h1 class="h3 fw-semibold mb-2">Detalle de Solicitud Completa</h1>
                <p class="mb-0 small opacity-90">Consulta la información detallada de la solicitud y los archivos adjuntos.</p>
            </div>
        </div>
    </div>

    <div class="container-fluid px-3 px-xl-4" style="max-width: 1200px; margin-inline: auto;">

        {{-- Información --}}
        <div class="card cli-detalle-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                    <h2 class="h5 fw-bold text-primary mb-0">Información de la Solicitud</h2>
                    @can('openClienteEdit', $solicitud)
                        <a href="{{ route('panel.cliente.solicitudes.edit', $solicitud) }}"
                           class="cli-btn-edit-legado-link text-decoration-none ms-auto"
                           title="{{ auth()->user()->can('update', $solicitud) ? 'Editar solicitud' : 'Consultar pantalla editar (solo guardar en estado Registrado)' }}"
                           aria-label="Editar solicitud">
                            <span class="cli-btn-edit-legado d-inline-flex align-items-center justify-content-center" aria-hidden="true">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                            </span>
                        </a>
                    @endcan
                </div>

                <div class="row g-3 small mb-3">
                            <div class="col-md-6 cli-detalle-col-sep pe-md-4">
                        <dl class="cli-detalle-grid mb-0">
                            <dt class="fw-normal mb-1">ID</dt>
                            <dd class="fw-semibold">{{ $solicitud->id }}</dd>
                            <dt class="fw-normal mb-1">Ciudad de Residencia</dt>
                            <dd>{{ trim((string) ($solicitud->ciudad_residencia_evaluado ?? '')) !== '' ? $solicitud->ciudad_residencia_evaluado : '—' }}</dd>
                            <dt class="fw-normal mb-1">Tipo de documento</dt>
                            <dd>{{ $tipoDocEtiqueta }}</dd>
                            <dt class="fw-normal mb-1">Documento</dt>
                            <dd>{{ trim((string) $solicitud->numero_documento) !== '' ? $solicitud->numero_documento : '—' }}</dd>
                            <dt class="fw-normal mb-1">Teléfono</dt>
                            <dd>{{ trim((string) ($solicitud->telefono_fijo ?? '')) !== '' ? $solicitud->telefono_fijo : '—' }}</dd>
                            <dt class="fw-normal mb-1">Celular</dt>
                            <dd>{{ trim((string) ($solicitud->celular ?? '')) !== '' ? $solicitud->celular : '—' }}</dd>
                            <dt class="fw-normal mb-1">Cliente Final</dt>
                            <dd>{{ trim((string) ($solicitud->cliente_final ?? '')) !== '' ? $solicitud->cliente_final : '—' }}</dd>
                        </dl>
                            </div>
                            <div class="col-md-6 ps-md-3">
                        <dl class="cli-detalle-grid mb-0">
                            <dt class="fw-normal mb-1">Estado</dt>
                            <dd>{{ $solicitud->estado }}</dd>
                            <dt class="fw-normal mb-1">Ciudad de solicitud de servicio</dt>
                            <dd>{{ trim((string) ($solicitud->ciudad_solicitud_servicio ?? '')) !== '' ? $solicitud->ciudad_solicitud_servicio : '—' }}</dd>
                            <dt class="fw-normal mb-1">Nombres</dt>
                            <dd>{{ trim((string) $solicitud->nombres) !== '' ? trim((string) $solicitud->nombres) : '—' }}</dd>
                            <dt class="fw-normal mb-1">Apellidos</dt>
                            <dd>{{ trim((string) $solicitud->apellidos) !== '' ? trim((string) $solicitud->apellidos) : '—' }}</dd>
                            <dt class="fw-normal mb-1">Regional</dt>
                            <dd>{{ trim((string) ($solicitud->ciudad_prestacion_servicio ?? '')) !== '' ? $solicitud->ciudad_prestacion_servicio : '—' }}</dd>
                            <dt class="fw-normal mb-1">Analista</dt>
                            <dd>{{ trim((string) ($solicitud->creador?->usuario ?? '')) !== '' ? $solicitud->creador->usuario : '—' }}</dd>
                            <dt class="fw-normal mb-1">Cargo</dt>
                            <dd>{{ trim((string) ($solicitud->cargo_candidato ?? '')) !== '' ? $solicitud->cargo_candidato : '—' }}</dd>
                        </dl>
                            </div>
                </div>

                @if ($nombrePaquete !== '' && $nombrePaquete !== '—')
                    <div class="cli-paquete-strip d-flex flex-wrap align-items-center gap-2 px-3 py-2 mt-3">
                        <span class="cli-paquete-badge-legado bg-primary text-white">
                            <i class="fas fa-box-open" aria-hidden="true"></i>
                            <span class="text-uppercase">Paquete</span>
                        </span>
                        <span class="fw-medium">{{ $nombrePaquete }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Archivos adjuntos --}}
        <div id="documentos" data-section="show-documentos-adjuntos" class="card cli-detalle-card mb-5">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <h2 class="h5 fw-bold text-primary mb-0">Archivos Adjuntos</h2>
                    @can('attachDocumentosCliente', $solicitud)
                        <button type="button"
                                class="btn btn-info cli-archivo-acc text-white shadow-none border-0"
                                data-bs-toggle="modal"
                                data-bs-target="#modalSubirDocumentoPdf"
                                title="Subir documento PDF">
                            <i class="fas fa-paperclip" aria-hidden="true"></i>
                            <span class="visually-hidden">Subir documento PDF</span>
                        </button>
                    @else
                        <span class="cli-archivo-acc d-inline-flex align-items-center justify-content-center bg-light border text-muted" title="Adjuntar documentos no disponible">
                            <i class="fas fa-paperclip" aria-hidden="true"></i>
                        </span>
                    @endcan
                </div>

                @php
                    $listaDoc = $solicitud->documentos;
                    $docsRespuesta = collect();
                    foreach (($solicitud->respuestasMadre ?? []) as $rm) {
                        foreach (($rm->documentos ?? []) as $docR) {
                            $docsRespuesta->push($docR);
                        }
                    }
                    $hayArchivos = $listaDoc->isNotEmpty() || $docsRespuesta->isNotEmpty();
                @endphp

                @if (! $hayArchivos)
                    <p id="lista-archivos-adjuntos" class="text-muted small mb-0">No hay documentos subidos.</p>
                @else
                    <ul class="list-group list-group-flush border rounded mb-3" id="lista-archivos-adjuntos">
                        @foreach ($listaDoc as $d)
                            <li class="list-group-item d-flex justify-content-between align-items-start gap-2">
                                <div class="d-flex gap-2 min-w-0">
                                    <i class="fas fa-file-pdf text-danger mt-1 flex-shrink-0" aria-hidden="true"></i>
                                    <div class="min-w-0 small">
                                        <div class="fw-semibold text-break">{{ $d->nombre_documento }}</div>
                                        <div class="text-danger" style="font-size:0.75rem;">Documento Solicitud</div>
                                    </div>
                                </div>
                                <a href="{{ route('panel.cliente.solicitudes.archivos.documento', ['solicitud' => $solicitud->id, 'documento' => $d->id]) }}"
                                   class="btn btn-primary cli-archivo-acc rounded-circle d-inline-flex align-items-center justify-content-center text-white"
                                   style="width:2.35rem;height:2.35rem;border-radius:50%!important;"
                                   title="Descargar"
                                   aria-label="Descargar {{ $d->nombre_documento }}">
                                    <i class="fas fa-download" aria-hidden="true"></i>
                                </a>
                            </li>
                        @endforeach
                        @foreach ($docsRespuesta as $dr)
                            <li class="list-group-item d-flex justify-content-between align-items-start gap-2">
                                <div class="d-flex gap-2 min-w-0">
                                    <i class="fas fa-file-pdf text-danger mt-1 flex-shrink-0" aria-hidden="true"></i>
                                    <div class="min-w-0 small">
                                        <div class="fw-semibold text-break">{{ $dr->nombre_documentoResp }}</div>
                                        <div class="text-danger" style="font-size:0.75rem;">Documento Respuesta</div>
                                    </div>
                                </div>
                                <a href="{{ route('panel.cliente.solicitudes.archivos.respuesta', ['solicitud' => $solicitud->id, 'documentoRespuesta' => $dr->id]) }}"
                                   class="btn btn-primary cli-archivo-acc rounded-circle d-inline-flex align-items-center justify-content-center text-white"
                                   style="width:2.35rem;height:2.35rem;border-radius:50%!important;"
                                   title="Descargar"
                                   aria-label="Descargar {{ $dr->nombre_documentoResp }}">
                                    <i class="fas fa-download" aria-hidden="true"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        @can('attachDocumentosCliente', $solicitud)
            <div class="modal fade" id="modalSubirDocumentoPdf" tabindex="-1" aria-labelledby="modalSubirDocumentoPdfLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form method="post" action="{{ route('panel.cliente.solicitudes.archivos.documento.store', $solicitud) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header cli-modal-pdf-header rounded-0 py-2 px-3">
                                <h2 class="modal-title h6 mb-0 fw-semibold" id="modalSubirDocumentoPdfLabel">Subir Documento PDF</h2>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body py-4 px-3">
                                <label for="modal_adjunto_pdf" class="form-label small fw-semibold text-body">Seleccionar archivo PDF</label>
                                <input id="modal_adjunto_pdf" type="file" name="documento" accept=".pdf,application/pdf" class="form-control @error('documento') is-invalid @enderror" required>
                                @error('documento')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="modal-footer cli-modal-pdf-footer rounded-0 py-2 px-3 gap-2 justify-content-end">
                                <button type="button" class="btn btn-sm btn-secondary text-uppercase fw-semibold px-3" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-sm btn-primary text-uppercase fw-semibold px-4">Subir</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

        {{-- VOLVER centrado tipo legado --}}
        <div class="text-center">
            @php
                $prevShow = url()->previous();
                $volverLista = (($prevShow !== url()->current()) && is_string($prevShow) && str_starts_with($prevShow, (string) config('app.url')))
                    ? $prevShow
                    : route('panel.cliente.solicitudes.index');
            @endphp
            <a href="{{ $volverLista }}" class="btn btn-secondary btn-lg px-5 fw-semibold text-uppercase">Volver</a>
        </div>

    </div>
</div>
@endsection
