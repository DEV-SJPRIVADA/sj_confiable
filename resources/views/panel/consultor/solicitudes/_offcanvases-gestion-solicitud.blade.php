@php
    $paq = $solicitud->paquete;
    $paqueteLinea1 = '—';
    if ($paq) {
        $n = trim((string) ($paq->nombre ?? ''));
        $d = trim((string) ($paq->descripcion ?? ''));
        if ($n !== '' && $d !== '') {
            $paqueteLinea1 = $n.': '.$d;
        } else {
            $paqueteLinea1 = $n !== '' ? $n : ($d !== '' ? $d : '—');
        }
    }
    $f = function ($v): string {
        if (is_string($v) && trim($v) !== '') {
            return $v;
        }
        if (is_numeric($v) && (string) $v !== '') {
            return (string) $v;
        }

        return '—';
    };
    $nDocs = $solicitud->documentos->count();
    $idPx = 'oc';
@endphp

<div class="offcanvas offcanvas-start solicitud-oc-gestion" tabindex="-1" id="ocDetalleSolicitud" aria-labelledby="ocDetalleSolicitudLabel" data-bs-scroll="true" style="--bs-offcanvas-width: min(420px, 100vw);">
    <div class="offcanvas-header solicitud-modal-detalle__bar text-white border-0">
        <h2 class="offcanvas-title h5 mb-0 fw-normal" id="ocDetalleSolicitudLabel">Detalle de Solicitud</h2>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body small">
        <p class="mb-1"><strong>ID:</strong> {{ $solicitud->id }}</p>
        <p class="mb-1"><strong>Estado:</strong> {{ $f($solicitud->estado) }}</p>
        <p class="mb-3"><strong>Fecha:</strong> {{ $solicitud->fecha_creacion?->format('d/m/Y h:i A') ?? '—' }}</p>
        <h3 class="h6 text-primary border-start border-4 border-primary ps-2 mb-2 fw-bold">Solicitud de (Paquete)</h3>
        <p class="text-break mb-3">{{ $paqueteLinea1 }}</p>
        <h3 class="h6 text-primary border-start border-4 border-primary ps-2 mb-2 fw-bold">Datos del Evaluado</h3>
        <p class="mb-1 text-break"><strong>Nombre completo:</strong> {{ \Illuminate\Support\Str::upper(trim(($solicitud->nombres ?? '').' '.($solicitud->apellidos ?? ''))) ?: '—' }}</p>
        <p class="mb-1"><strong>Identificación:</strong> {{ $f($solicitud->tipo_identificacion) }} {{ $f($solicitud->numero_documento) }}</p>
        <p class="mb-1"><strong>Fecha expedición:</strong> {{ $solicitud->fecha_expedicion?->format('d/m/Y') ?? '—' }}</p>
        <p class="mb-1 text-break"><strong>Lugar expedición:</strong> {{ $f($solicitud->lugar_expedicion) }}</p>
        <p class="mb-1"><strong>Celular:</strong> {{ $f($solicitud->celular) }}</p>
        <p class="mb-1 text-break"><strong>Ciudad de residencia:</strong> {{ $f($solicitud->ciudad_residencia_evaluado) }}</p>
        <p class="mb-1 text-break"><strong>Dirección:</strong> {{ $f($solicitud->direccion_residencia) }}</p>
        <p class="mb-3 text-break"><strong>Cargo:</strong> {{ $f($solicitud->cargo_candidato) }}</p>
        <h3 class="h6 text-primary border-start border-4 border-primary ps-2 mb-2 fw-bold">Detalles de la Solicitud</h3>
        <p class="mb-1 text-break"><strong>Empresa solicitante:</strong> {{ $f($solicitud->empresa_solicitante) }}</p>
        <p class="mb-1 text-break"><strong>Cliente final:</strong> {{ $f($solicitud->cliente_final) }}</p>
        <p class="mb-1 text-break"><strong>NIT empresa:</strong> {{ $f($solicitud->nit_empresa_solicitante) }}</p>
        <p class="mb-1 text-break"><strong>Ciudad prestación servicio:</strong> {{ $f($solicitud->ciudad_prestacion_servicio) }}</p>
        <p class="mb-1 text-break"><strong>Ciudad solicitud servicio:</strong> {{ $f($solicitud->ciudad_solicitud_servicio) }}</p>
        <p class="mb-3 text-break"><strong>Teléfono fijo:</strong> {{ $f($solicitud->telefono_fijo) }}</p>
        <div class="d-grid">
            <a class="btn btn-sm btn-info text-white text-uppercase fw-semibold d-inline-flex align-items-center justify-content-center gap-2" href="#{{ $idPx }}-documentos" id="btnIrDocumentosGestion" data-oc-scroll-doc>
                <img src="{{ asset('images/pdf.png') }}" alt="" width="18" height="18" class="solicitud-oc-pdf-thumb">
                <span>Ver documentos ({{ $nDocs }})</span>
            </a>
        </div>
        <div class="mt-3" id="bloqueDocumentosGestion">
            @include('panel.solicitudes._fragment-documentos-solicitud', ['solicitud' => $solicitud, 'idPrefix' => $idPx])
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="ocHistorialSolicitud" aria-labelledby="ocHistorialSolicitudLabel" data-bs-scroll="true" style="--bs-offcanvas-width: min(480px, 100vw);">
    <div class="offcanvas-header border-bottom">
        <h2 class="offcanvas-title h5 text-primary mb-0 fw-bold" id="ocHistorialSolicitudLabel">Historial</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="p-2 p-md-3">
            @include('panel.solicitudes._fragment-historial-respuestas', ['solicitud' => $solicitud, 'idPrefix' => 'oc'])
        </div>
    </div>
</div>
