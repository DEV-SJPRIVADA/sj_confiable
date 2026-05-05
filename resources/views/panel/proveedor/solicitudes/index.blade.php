@extends('layouts.app')

@section('title', 'Solicitudes confiables asignadas')

@push('styles')
<style>
    .prov-solic-legado-scope {
        margin-inline: calc(-1 * var(--bs-gutter-x, 1.5rem));
        padding-inline: clamp(0.35rem, 2vw, 1rem);
        padding-bottom: 2rem;
        background: #f1f3f5;
    }
    .prov-solic-titulo-legacy-wrap {
        text-align: center;
        padding-top: 0.25rem;
        margin-bottom: 1rem;
    }
    .prov-solic-titulo-legacy {
        color: #0d62a8;
        font-size: clamp(1.35rem, 2.8vw, 1.95rem);
        letter-spacing: 0.015em;
    }
    .prov-solic-sub {
        font-size: 0.88rem;
    }
    .prov-solic-toolbar.row-cols-toolbar {
        max-width: 100%;
        margin-bottom: 0.65rem;
    }
    .tabla-solicitudes-prov-wrap,
    .prov-solic-legado-scope .table-responsive.rounded-legacy {
        width: 100%;
    }
    .tabla-solicitudes-prov thead th {
        vertical-align: middle;
        white-space: nowrap;
        padding: 0.5rem 0.55rem !important;
        border-color: #ced4da !important;
    }
    .tabla-solicitudes-prov-fila td {
        border-color: #dee2e6;
        vertical-align: middle;
        font-size: 0.88rem;
    }
    .tabla-solicitudes-prov-fila--completado td {
        background: #ccefd6 !important;
        color: #14532d !important;
    }
    .tabla-solicitudes-prov-fila--cancelado td {
        background: #fde0e9 !important;
        color: #8f1d39 !important;
    }
    .prov-solic-round-btn {
        display: inline-flex;
        width: 2.05rem;
        height: 2.05rem;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        background: linear-gradient(180deg, #178ba5 0%, #0f6f84 100%);
        color: #fff !important;
        text-decoration: none !important;
        font-size: 0.88rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: filter 0.15s ease, transform 0.12s ease;
    }
    .prov-solic-round-btn:hover {
        filter: brightness(1.05);
        color: #fff !important;
    }
    .prov-solic-round-btn--muted {
        background: linear-gradient(180deg, #138aa3 0%, #0b5f74 100%);
    }
    button.prov-solic-round-btn {
        appearance: none;
        -webkit-appearance: none;
        border: none;
        padding: 0;
        cursor: pointer;
        line-height: 1;
    }
    /* Modal detalle (mismo criterio que consultor / legado proveedor_solicitudes) */
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
    .solicitud-modal-detalle__btn-cerrar {
        border-width: 2px;
        font-weight: 600;
    }
    .solicitud-modal-detalle__btn-cerrar:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="prov-solic-legado-scope">
    <div class="prov-solic-titulo-legacy-wrap">
        <h1 class="prov-solic-titulo-legacy mb-2">Solicitudes Confiables Asignadas</h1>
        <p class="prov-solic-sub text-muted mb-0">Solicitudes asignadas a su asociado. La mediación con el cliente la gestiona SJ Seguridad.</p>
    </div>

    @php
        $tieneSol = isset($solicitudes) ? $solicitudes->count() : 0;
    @endphp

    <div class="row row-cols-1 row-cols-md-2 gx-3 gy-2 prov-solic-toolbar align-items-md-center px-1">
        <div class="col d-flex align-items-center flex-wrap gap-2">
            <label class="small mb-0 text-nowrap fw-medium text-secondary" for="tamPaginaProvSol">Mostrar</label>
            <select id="tamPaginaProvSol" class="form-select form-select-sm" style="width: auto; min-width: 4rem;" aria-controls="tbodyPanelSolicitudes">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="9999">Todos</option>
            </select>
            <span class="small text-muted">registros</span>
        </div>
        <div class="col d-flex align-items-center justify-content-md-end gap-2">
            <label class="small mb-0 text-nowrap fw-medium text-secondary" for="buscarProvSol">Buscar:</label>
            <input type="search" id="buscarProvSol" class="form-control form-control-sm" placeholder="" autocomplete="off" aria-label="Buscar solicitudes" style="max-width: min(28rem, 100%);">
        </div>
    </div>

    @include('panel._tabla-solicitudes', [
        'solicitudes' => $solicitudes,
        'detalleRoute' => $detalleRoute,
        'listaEstilo' => 'proveedor-legacy',
        'proveedorGestionRespuestaRoute' => 'panel.proveedor.solicitudes.respuesta',
    ])

    @foreach ($solicitudes as $s)
        @include('panel.consultor.solicitudes._modal-detalle-solicitud', ['s' => $s])
    @endforeach

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-stretch align-items-lg-center gap-2 mt-3 px-1">
        <p class="small text-muted mb-0 order-lg-0" id="panelProveedorSolResumen" aria-live="polite"></p>
        @if ($tieneSol > 0)
            <div class="btn-group btn-group-sm panel-prov-solic-pagination order-lg-1" role="group" aria-label="Paginación">
                <button type="button" class="btn btn-outline-secondary" disabled>Primero</button>
                <button type="button" class="btn btn-outline-secondary" disabled>Anterior</button>
                <button type="button" class="btn btn-primary disabled" disabled aria-current="page">1</button>
                <button type="button" class="btn btn-outline-secondary" disabled>Siguiente</button>
                <button type="button" class="btn btn-outline-secondary" disabled>Último</button>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@php
    $__provAbrirModalSid = session()->pull('open_modal_solicitud_id');
@endphp
<script>
(function () {
    var tbody = document.getElementById('tbodyPanelSolicitudes');
    var input = document.getElementById('buscarProvSol');
    var sel = document.getElementById('tamPaginaProvSol');
    var resumen = document.getElementById('panelProveedorSolResumen');
    var sinRes = document.getElementById('tbodyProvSinResultadosBusqueda');
    var msgVacio = tbody ? tbody.querySelector('tr.tabla-solicitudes-msg-vacio') : null;
    if (!tbody || !input || !sel) return;

    function norm(s) {
        try {
            return (s || '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
        } catch (e) {
            return (s || '').toLowerCase();
        }
    }

    var filas = Array.prototype.slice.call(tbody.querySelectorAll(
        'tr.tabla-solicitudes-prov-fila'
    ));

    function apply() {
        var q = norm(input.value);
        var matched = [];

        filas.forEach(function (tr) {
            var texto = norm(tr.innerText || '');
            var ok = !q || texto.indexOf(q) !== -1;
            if (ok) matched.push(tr);
        });

        var maxFilas = parseInt(sel.value, 10);
        if (isNaN(maxFilas) || maxFilas < 1) maxFilas = 9999;

        filas.forEach(function (tr) {
            tr.classList.add('d-none');
        });

        matched.slice(0, maxFilas).forEach(function (tr) {
            tr.classList.remove('d-none');
        });

        var totalMatch = matched.length;
        var visibles = Math.min(totalMatch, maxFilas);

        if (sinRes) {
            sinRes.classList.toggle('d-none', !(totalMatch === 0 && filas.length > 0));
        }
        if (msgVacio) {
            if (filas.length > 0) {
                msgVacio.classList.add('d-none');
            } else {
                msgVacio.classList.remove('d-none');
            }
        }

        if (resumen) {
            if (filas.length === 0) {
                resumen.textContent = '';
            } else if (totalMatch === 0 && filas.length > 0) {
                resumen.textContent = 'Mostrando 0 de ' + filas.length + ' registros (sin coincidencias).';
            } else if (totalMatch > 0) {
                resumen.textContent = 'Mostrando 1 a ' + visibles + ' de ' + totalMatch + ' registros.';
            }
        }
    }

    input.addEventListener('input', apply);
    sel.addEventListener('change', apply);
    apply();
})();
</script>
@if ($__provAbrirModalSid)
<script>
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var id = {{ (int) $__provAbrirModalSid }};
        var el = document.getElementById('modalDetalleSolicitud' + id);
        if (el && window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(el).show();
        }
    });
})();
</script>
@endif
@endpush
