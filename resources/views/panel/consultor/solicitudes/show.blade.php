@extends('layouts.app')

@section('title', 'Solicitud #'.$solicitud->id)

@push('styles')
<style>
    .solicitud-gestion-btn-aux {
        background: #f8f9fa;
        border: 1px solid #dee2e6 !important;
        color: #0d3a66;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
    }
    .solicitud-gestion-btn-aux:hover { background: #fff; color: #062a58; }
    .solicitud-gestion-volver {
        color: #2c3e50;
        border: 1px solid #ced4da;
        background: #fff;
    }
    .solicitud-gestion-volver:hover { background: #f8f9fa; color: #0b2239; }
    @media (min-width: 576px) {
        .solicitud-gestion__btn-enviar { max-width: 22rem; }
    }
    .solicitud-modal-detalle__bar {
        background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%) !important;
    }
    .solicitud-oc-gestion .card { border: 1px solid #e9ecef; }
    .solicitud-nueva-respuesta textarea { min-height: 7rem; }
    .solicitud-gestion-hero__grid {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 0.5rem 0.25rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 575.98px) {
        .solicitud-gestion-hero__grid { grid-template-columns: 1fr; text-align: center; }
        .solicitud-gestion-hero__grid .solicitud-gestion-hero__l { justify-content: center; }
        .solicitud-gestion-hero__grid .solicitud-gestion-hero__r { justify-content: center; }
    }
</style>
@endpush

@section('content')
@php
    $st = (string) ($solicitud->estado ?? 'Nuevo');
    $etiquetaEstado = function (string $clave): string {
        return match ($clave) {
            'En proceso' => 'En Proceso',
            'Nuevo' => 'Nuevo',
            'Completado' => 'Completado',
            'Cancelado' => 'Cancelado',
            default => $clave,
        };
    };
    $estadoOpciones = [
        'Nuevo' => 'Nuevo', 'En proceso' => 'En Proceso', 'Completado' => 'Completado', 'Cancelado' => 'Cancelado',
    ];
    if ($st !== '' && ! array_key_exists($st, $estadoOpciones)) {
        $estadoOpciones = [$st => $etiquetaEstado($st)] + $estadoOpciones;
    }
@endphp
<div class="solicitud-gestion-hero__grid">
    <div class="d-flex align-items-center gap-2 flex-wrap solicitud-gestion-hero__l">
        <a href="{{ route('panel.consultor.solicitudes.index') }}" class="btn btn-sm solicitud-gestion-volver d-inline-flex align-items-center text-uppercase shadow-sm" title="Volver al listado de solicitudes" aria-label="Volver al listado de solicitudes">
            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
            <span>Volver</span>
        </a>
        <button type="button" class="btn btn-sm solicitud-gestion-btn-aux shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#ocDetalleSolicitud" aria-controls="ocDetalleSolicitud">
            <i class="fas fa-info-circle me-1" aria-hidden="true"></i>Detalle
        </button>
    </div>
    <h1 class="h4 mb-0">Solicitud #{{ $solicitud->id }}</h1>
    <div class="d-flex justify-content-end solicitud-gestion-hero__r">
        <button type="button" class="btn btn-sm solicitud-gestion-btn-aux shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#ocHistorialSolicitud" aria-controls="ocHistorialSolicitud">
            <i class="fas fa-history me-1" aria-hidden="true"></i>Historial
        </button>
    </div>
</div>

@can('manageAsConsultor', $solicitud)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white text-primary fw-bold">Nueva respuesta</div>
    <div class="card-body solicitud-nueva-respuesta">
        <p class="text-muted small mb-3 mb-md-4">Escriba el mensaje que verá la organización cliente en el historial. Los PDF opcionales se guardan como documentos de la solicitud.</p>
        @if ($errors->any())
            <div class="alert alert-danger small mb-3" role="alert">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('panel.consultor.solicitudes.respuesta', $solicitud) }}" enctype="multipart/form-data" id="formNuevaRespuestaConsultor">
            @csrf
            <div class="mb-3">
                <label for="nueva_respuesta_borrador" class="form-label fw-semibold">Respuesta</label>
                <textarea class="form-control @error('nueva_respuesta') is-invalid @enderror" id="nueva_respuesta_borrador" name="nueva_respuesta" rows="5" maxlength="20000" required placeholder="Informe aquí la respuesta o actualización">{{ old('nueva_respuesta') }}</textarea>
                @error('nueva_respuesta')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <span class="form-label fw-semibold d-block">Documentos adjuntos (Máx. 10 PDFs)</span>
                <div class="input-group">
                    <input type="file" class="form-control @error('documentos') is-invalid @enderror @error('documentos.*') is-invalid @enderror" id="consultor_adjuntos_pdf" name="documentos[]" accept=".pdf,application/pdf" multiple aria-label="Documentos adjuntos PDF">
                </div>
                @error('documentos')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('documentos.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <p class="text-muted small mt-1 mb-0" id="consultorAdjuntosCuenta">Archivos seleccionados: 0/10</p>
            </div>
            <div class="mb-4">
                <label for="nuevo_estado_solicitud" class="form-label fw-semibold">Nuevo estado</label>
                <select id="nuevo_estado_solicitud" name="nuevo_estado" class="form-select @error('nuevo_estado') is-invalid @enderror" required>
                    @foreach ($estadoOpciones as $val => $label)
                        <option value="{{ $val }}" @selected(old('nuevo_estado', $st) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('nuevo_estado')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="d-grid gap-2 mx-auto solicitud-gestion__btn-enviar">
                <button type="submit" class="btn btn-primary btn-lg text-uppercase fw-semibold py-2">Enviar respuesta</button>
            </div>
        </form>
    </div>
</div>
@endcan

@can('assignToProveedor', $solicitud)
    @php
        $clienteFinalVal = (string) old('cliente_final', (string) ($solicitud->cliente_final ?? ''));
    @endphp
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white text-primary fw-bold">Asignación de asociado de negocio</div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif
            <p class="text-muted small mb-3">Elija el asociado; el cambio se registra en historial y notifica al solicitante (flujo alineado al legado, sin reenviar correos en esta fase).</p>
            <form method="post" action="{{ route('panel.consultor.solicitudes.asignar', $solicitud) }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="cliente_final" class="form-label fw-semibold">Cliente final</label>
                        <input type="text" class="form-control @if(trim($clienteFinalVal) !== '') bg-body-secondary @endif" name="cliente_final" id="cliente_final" maxlength="150" value="{{ $clienteFinalVal }}" inputmode="text" autocomplete="off" placeholder="(Opcional)">
                    </div>
                    <div class="col-md-6">
                        <label for="tipo_cliente" class="form-label fw-semibold">Tipo cliente</label>
                        <select name="tipo_cliente" id="tipo_cliente" class="form-select">
                            <option value="">— N/C —</option>
                            <option value="Interno" @selected(old('tipo_cliente', $solicitud->tipo_cliente) === 'Interno')>Interno</option>
                            <option value="Externo" @selected(old('tipo_cliente', $solicitud->tipo_cliente) === 'Externo')>Externo</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="id_proveedor" class="form-label fw-semibold">Asociado de negocio</label>
                    <select name="id_proveedor" id="id_proveedor" class="form-select" required>
                        <option value="">Seleccione un asociado de negocio…</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id_proveedor }}" @selected((int) $solicitud->id_proveedor === (int) $p->id_proveedor)>
                                {{ $p->razon_social_proveedor }} ({{ $p->nombre_comercial }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex flex-wrap">
                    <button type="submit" class="btn btn-outline-primary text-uppercase fw-semibold px-4 d-inline-flex align-items-center gap-2">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                        <span>Asignar y procesar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endcan

@include('panel.consultor.solicitudes._offcanvases-gestion-solicitud', ['solicitud' => $solicitud])
@endsection

@push('scripts')
<script>
(function () {
    function abrirYHash() {
        const h = window.location.hash;
        if (h === '#documentos' || h === '#panel-solicitud-documentos') {
            const el = document.getElementById('ocDetalleSolicitud');
            if (el) {
                const oc = window.bootstrap.Offcanvas.getOrCreateInstance(el);
                oc.show();
                setTimeout(function () {
                    var t = document.getElementById('oc-documentos');
                    if (t) { t.scrollIntoView({ block: 'start', behavior: 'smooth' }); }
                }, 400);
            }
            return;
        }
        if (h === '#historial' || h === '#panel-solicitud-historial') {
            var ocr = document.getElementById('ocHistorialSolicitud');
            if (ocr) { window.bootstrap.Offcanvas.getOrCreateInstance(ocr).show(); }
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        abrirYHash();
        var fi = document.getElementById('consultor_adjuntos_pdf');
        var cnt = document.getElementById('consultorAdjuntosCuenta');
        if (fi && cnt) {
            fi.addEventListener('change', function () {
                var n = fi.files ? fi.files.length : 0;
                if (n > 10) {
                    cnt.textContent = 'Máximo 10 archivos. Se usarán sólo los primeros 10 al enviar.';
                    cnt.classList.add('text-warning');
                } else {
                    cnt.textContent = 'Archivos seleccionados: ' + n + '/10';
                    cnt.classList.remove('text-warning');
                }
            });
        }
    });
    document.querySelectorAll('[data-oc-scroll-doc]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            var t = document.getElementById('oc-documentos');
            if (t) { t.scrollIntoView({ block: 'start', behavior: 'smooth' }); }
        });
    });
})();
</script>
@endpush
