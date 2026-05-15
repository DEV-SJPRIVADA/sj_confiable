@extends('layouts.app')

@section('title', 'Mis solicitudes — Cliente')

@push('styles')
<style>
    /* Casi borde a borde dentro del área útil del main */
    .panel-cliente-solicitudes-lista {
        margin-inline: calc(-1 * var(--bs-gutter-x, 1.5rem));
        padding-inline: clamp(0.35rem, 2vw, 0.85rem);
        padding-bottom: 1.75rem;
    }
    /* FAB más abajo y solapando la zona de controles (como DataTables viejo) */
    .panel-cli-solic-fab-strip {
        position: relative;
        z-index: 6;
        margin-top: 1.75rem;
        margin-bottom: -2rem;
        padding-bottom: 0.85rem;
    }
    .solicitudes-fab-nueva {
        width: 3.65rem;
        height: 3.65rem;
        font-size: 1.4rem;
        box-shadow: 0 0.35rem 0.85rem rgba(13, 110, 253, 0.45);
        border-radius: 50% !important;
    }
    .panel-cli-solic-toolbar {
        flex-wrap: wrap;
        gap: 0.5rem;
        position: relative;
        z-index: 2;
        padding-top: 2.85rem !important;
        margin-bottom: 0.6rem !important;
    }

    .tabla-solicitudes-cli-th-sort {
        font-weight: 400;
        display: inline;
    }

    /* Paridad visual con listado legacy (cabecera oscura / filas por estado) */
    .panel-cliente-solicitudes-lista .tabla-solicitudes-cli-thead th {
        background: linear-gradient(180deg, rgb(22, 15, 62) 0%, rgb(18, 20, 48) 100%) !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.8rem !important;
        border-color: rgba(255, 255, 255, 0.18) !important;
        vertical-align: middle;
    }
    .panel-cliente-solicitudes-lista .servicios-legado-cell { max-width: min(38rem, 100%); }
    .panel-cliente-solicitudes-lista .tabla-solicitudes-cli-evaluado {
        text-transform: uppercase;
        font-weight: 500;
    }
    .panel-cliente-solicitudes-lista .tabla-solicitudes-cli-fila td {
        border-color: #dee2e6;
        vertical-align: middle;
    }
    @include('panel.partials._styles-legacy-fila-estado-solicitud')

    @include('panel.partials._styles-cliente-acciones-solicitud')

    /* Paginación estilo viejo (informativa; servidor no pagina en este listado) */
    .panel-cli-solic-pagination .btn[disabled],
    .panel-cli-solic-pagination .btn.disabled {
        opacity: 0.75;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="panel-cliente-solicitudes-lista">
    <div class="panel-cli-solic-fab-strip text-center">
        <a href="{{ route('panel.cliente.solicitudes.create') }}"
           class="btn btn-primary rounded-circle solicitudes-fab-nueva d-inline-flex align-items-center justify-content-center text-white"
           title="Nueva solicitud"
           aria-label="Nueva solicitud">
            <span class="visually-hidden">Nueva solicitud</span>
            <i class="fas fa-plus" aria-hidden="true"></i>
        </a>
    </div>

    <div class="d-flex panel-cli-solic-toolbar justify-content-between align-items-center mb-2 px-1 bg-transparent">
        <div class="d-flex align-items-center gap-2">
            <label class="small mb-0 text-nowrap fw-medium text-secondary" for="tamPaginaClienteSol">Mostrar</label>
            <select id="tamPaginaClienteSol" class="form-select form-select-sm" style="width: auto; min-width: 4rem;" aria-controls="tbodyPanelSolicitudes">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="9999">Todos</option>
            </select>
            <span class="small text-muted">registros</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0 justify-content-md-end" style="min-width: min(100%, 18rem);">
            <label class="small mb-0 text-nowrap fw-medium text-secondary" for="buscarClienteSol">Buscar:</label>
            <input type="search" id="buscarClienteSol" class="form-control form-control-sm" placeholder="Buscar en la tabla" autocomplete="off" aria-label="Buscar solicitudes">
        </div>
    </div>

    @php
        $tieneSol = isset($solicitudes) ? $solicitudes->count() : 0;
    @endphp
    @include('panel._tabla-solicitudes', [
        'solicitudes' => $solicitudes,
        'detalleRoute' => $detalleRoute,
        'listaEstilo' => 'cliente-legacy',
    ])

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-stretch align-items-lg-center gap-2 mt-3 px-1">
        <p class="small text-muted mb-0 order-lg-0" id="panelClienteSolResumen" aria-live="polite"></p>
        @if ($tieneSol > 0)
            <div class="btn-group btn-group-sm panel-cli-solic-pagination order-lg-1" role="group" aria-label="Paginación del listado">
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
<script>
(function () {
    var tbody = document.getElementById('tbodyPanelSolicitudes');
    var input = document.getElementById('buscarClienteSol');
    var sel = document.getElementById('tamPaginaClienteSol');
    var resumen = document.getElementById('panelClienteSolResumen');
    var sinRes = document.getElementById('tbodyClienteSinResultadosBusqueda');
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
        'tr.tabla-solicitudes-cli-fila:not(.tabla-solicitudes-msg-vacio)'
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
@endpush
