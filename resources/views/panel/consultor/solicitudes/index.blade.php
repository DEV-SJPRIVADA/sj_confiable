@extends('layouts.app')

@section('title', 'Gestión de Solicitudes — Consultor')

@push('styles')
<style>
    .table-legacy.solicitudes-gestion-table { table-layout: auto; }
    .table-legacy.solicitudes-gestion-table thead th {
        background: #e9ecef !important;
        color: #212529 !important;
        font-weight: 600;
        font-size: 0.8rem;
        border-color: #dee2e6 !important;
        white-space: nowrap;
        vertical-align: middle;
    }
    .table-legacy.solicitudes-gestion-table thead th a {
        color: #0d3a66 !important;
        text-decoration: none;
    }
    .table-legacy.solicitudes-gestion-table thead th a:hover { color: #0a58ca !important; }
    .table-legacy.solicitudes-gestion-table thead th i { color: #6c757d !important; }
    .solicitudes-gestion-table tbody td { font-size: 0.875rem; vertical-align: middle; }
    .solicitudes-gestion-table .col-evaluado { text-transform: uppercase; }
    .solicitudes-gestion-toolbar { gap: 0.75rem; }
    .solicitudes-gestion-toolbar__page-size,
    .solicitudes-gestion-toolbar__search {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.35rem;
    }
    .solicitudes-gestion-toolbar__search .input-group { min-width: 12rem; }
    .solicitudes-gestion__vista .btn { min-width: 6.5rem; }
    tr.solicitud-gestion-fila--proceso td {
        background: #fff9c4 !important;
        color: #212529;
    }
    tr.solicitud-gestion-fila--default td {
        background: #2c3a57 !important;
        color: #f8f9fa;
    }
    tr.solicitud-gestion-fila--default a:not(.solicitud-accion-btn) { color: #9ec5fe; }
    .solicitud-acciones {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        flex-wrap: nowrap;
    }
    .solicitud-accion-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.85rem;
        height: 1.85rem;
        padding: 0;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        color: #fff;
        line-height: 1;
    }
    .solicitud-accion-btn--doc {
        background: linear-gradient(180deg, #5eb3e0 0%, #4fa3d1 100%);
        gap: 0.3rem;
        padding: 0.15rem 0.3rem 0.15rem 0.2rem;
        min-width: 2.75rem;
        height: 1.85rem;
    }
    .solicitud-accion-btn--doc:hover {
        background: linear-gradient(180deg, #4a9bca 0%, #3d8ab8 100%);
        color: #fff;
    }
    .solicitud-doc-pdf-img {
        display: block;
        width: auto;
        height: 1.15rem;
        object-fit: contain;
        flex-shrink: 0;
    }
    @media (min-width: 768px) {
        .solicitud-doc-pdf-img { height: 1.25rem; }
    }
    .solicitud-doc-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.1rem;
        height: 1.15rem;
        padding: 0 0.2rem;
        font-size: 0.65rem;
        font-weight: 800;
        line-height: 1;
        color: #1e293b;
        background: #e8ecf0;
        border-radius: 999px;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.12);
        flex-shrink: 0;
    }
    tr.solicitud-gestion-fila--proceso .solicitud-doc-badge {
        background: #e8ecf0;
        color: #1e293b;
    }
    .solicitud-accion-btn--ver { background: #1e3a5c; }
    .solicitud-accion-btn--ver:hover { background: #0f2844; color: #fff; }
    .solicitud-accion-btn--hist { background: #2d8a54; }
    .solicitud-accion-btn--hist:hover { background: #236e43; color: #fff; }
    tr.solicitud-gestion-fila--proceso .solicitud-accion-btn--ver { color: #fff; }
    tr.solicitud-gestion-fila--proceso .solicitud-accion-btn--doc { color: #fff; }
    tr.solicitud-gestion-fila--proceso .solicitud-accion-btn--hist { color: #fff; }
    .solicitud-accion-btn--doc-sin-enlace {
        pointer-events: none;
        cursor: default;
    }
    .solicitud-accion-btn--doc-sin-enlace { opacity: 0.88; }
    .solicitud-accion-btn--ver i,
    .solicitud-accion-btn--hist i { font-size: 0.88rem; }
    button.solicitud-accion-btn {
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
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
@php
    $vista = $vista ?? 'activas';
    $baseQuery = array_filter([
        'vista' => $vista,
        'per_page' => $perPage,
        'q' => $q !== '' ? $q : null,
        'sort' => $sort,
        'dir' => $dir,
    ], fn ($v) => $v !== null && $v !== '');
    $sortLink = function (string $col) use ($baseQuery, $sort, $dir): string {
        $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
        $q = array_merge($baseQuery, [
            'sort' => $col,
            'dir' => $nextDir,
        ]);
        return route('panel.consultor.solicitudes.index', $q);
    };
    $sortIcon = function (string $col) use ($sort, $dir): string {
        if ($sort !== $col) {
            return 'fa-sort text-muted';
        }
        return $dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    };
    $ciudadFila = function ($s): string {
        $a = $s->ciudad_solicitud_servicio ?? '';
        if ($a !== '' && is_string($a)) {
            return $a;
        }
        $b = $s->ciudad_residencia_evaluado ?? '';
        return is_string($b) ? $b : '—';
    };
    $enviadoPor = function ($s): string {
        $c = $s->creador?->persona?->correo ?? null;
        if (is_string($c) && $c !== '') {
            return $c;
        }
        return $s->creador?->usuario ?? '—';
    };
    $filaProceso = function ($s): bool {
        $e = (string) ($s->estado ?? '');
        return strcasecmp($e, 'En proceso') === 0;
    };
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
    <h1 class="fw-light mb-0" style="font-size:1.75rem;">Gestión de Solicitudes</h1>
    <div class="solicitudes-gestion__vista btn-group" role="group" aria-label="Solicitudes activas o inactivas">
        <a href="{{ route('panel.consultor.solicitudes.index', array_merge($baseQuery, ['vista' => 'activas'])) }}"
           class="btn btn-sm @if($vista === 'activas') btn-primary @else btn-outline-primary @endif text-uppercase fw-semibold">Activas</a>
        <a href="{{ route('panel.consultor.solicitudes.index', array_merge($baseQuery, ['vista' => 'inactivas'])) }}"
           class="btn btn-sm @if($vista === 'inactivas') btn-primary @else btn-outline-primary @endif text-uppercase fw-semibold">Inactivas</a>
    </div>
</div>

<form method="get" action="{{ route('panel.consultor.solicitudes.index') }}" class="mb-3">
    <input type="hidden" name="vista" value="{{ $vista }}">
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir" value="{{ $dir }}">
    <div class="d-flex flex-wrap align-items-center justify-content-between solicitudes-gestion-toolbar">
        <div class="solicitudes-gestion-toolbar__page-size text-muted small">
            <span>Mostrar</span>
            <select name="per_page" class="form-select form-select-sm" style="width: auto; min-width: 4.25rem; max-width: 5rem; padding-top: 0.2rem; padding-bottom: 0.2rem; flex: 0 0 auto;" onchange="this.form.submit()" aria-label="Registros por página">
                @foreach ([10, 25, 50, 100] as $n)
                    <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                @endforeach
            </select>
            <span>registros</span>
        </div>
        <div class="solicitudes-gestion-toolbar__search text-muted small">
            <label for="buscar_solicitudes" class="mb-0 text-nowrap">Buscar:</label>
            <div class="input-group input-group-sm">
                <input type="search" name="q" id="buscar_solicitudes" class="form-control" value="{{ $q }}" placeholder="ID, evaluado, documento, ciudad, estado, cliente…" autocomplete="off">
                <button type="submit" class="btn btn-outline-secondary">Buscar</button>
            </div>
        </div>
    </div>
</form>

<div class="table-responsive rounded-legacy bg-white shadow-sm">
    <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0 solicitudes-gestion-table" id="tablaGestionSolicitudes">
        <thead>
        <tr>
            <th scope="col" class="text-nowrap">
                <a href="{{ $sortLink('id') }}">ID <i class="fas {{ $sortIcon('id') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('evaluado') }}">Evaluado <i class="fas {{ $sortIcon('evaluado') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-nowrap">
                <a href="{{ $sortLink('documento') }}">Documento <i class="fas {{ $sortIcon('documento') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('ciudad') }}">Ciudad <i class="fas {{ $sortIcon('ciudad') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-nowrap">
                <a href="{{ $sortLink('fecha') }}">Fecha creación <i class="fas {{ $sortIcon('fecha') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-nowrap">
                <a href="{{ $sortLink('estado') }}">Estado <i class="fas {{ $sortIcon('estado') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('enviado') }}">Enviado por <i class="fas {{ $sortIcon('enviado') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('cliente') }}">Cliente <i class="fas {{ $sortIcon('cliente') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-center text-nowrap">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($solicitudes as $s)
            <tr @class(['solicitud-gestion-fila--proceso' => $filaProceso($s), 'solicitud-gestion-fila--default' => ! $filaProceso($s)])>
                <td class="text-nowrap">
                    <a href="{{ route($detalleRoute, $s) }}" class="text-decoration-none fw-semibold @if($filaProceso($s)) text-body @else text-info @endif">{{ $s->id }}</a>
                </td>
                <td class="col-evaluado">{{ \Illuminate\Support\Str::upper(trim(($s->nombres ?? '').' '.($s->apellidos ?? ''))) ?: '—' }}</td>
                <td class="text-nowrap">{{ $s->tipo_identificacion }}: {{ $s->numero_documento }}</td>
                <td>{{ $ciudadFila($s) ?: '—' }}</td>
                <td class="text-nowrap">{{ $s->fecha_creacion?->format('d/m/Y h:i A') ?? '—' }}</td>
                <td class="text-nowrap">{{ $s->estado ?? '—' }}</td>
                <td>{{ $enviadoPor($s) }}</td>
                <td>{{ $s->creador?->cliente?->razon_social ?? '—' }}</td>
                <td class="text-center text-nowrap">
                    <div class="solicitud-acciones">
                        @php $nDocFila = (int) ($s->documentos_count ?? 0); @endphp
                        @if ($nDocFila > 0)
                            <a href="{{ route($detalleRoute, $s) }}#documentos" class="solicitud-accion-btn solicitud-accion-btn--doc" aria-label="Ver documentos ({{ $nDocFila }}) de la solicitud">
                                <img src="{{ asset('images/pdf.png') }}" alt="" class="solicitud-doc-pdf-img" loading="lazy" decoding="async">
                                <span class="solicitud-doc-badge">{{ $nDocFila }}</span>
                            </a>
                        @else
                            <span class="solicitud-accion-btn solicitud-accion-btn--doc solicitud-accion-btn--doc-sin-enlace" role="img" aria-label="Sin documentos en esta solicitud">
                                <img src="{{ asset('images/pdf.png') }}" alt="" class="solicitud-doc-pdf-img" loading="lazy" decoding="async">
                                <span class="solicitud-doc-badge">0</span>
                            </span>
                        @endif
                        <button type="button" class="solicitud-accion-btn solicitud-accion-btn--ver" data-bs-toggle="modal" data-bs-target="#modalDetalleSolicitud{{ $s->id }}" title="Ver detalle" aria-label="Detalle de solicitud (ventana emergente)"><i class="fas fa-search" aria-hidden="true"></i></button>
                        <a href="{{ route($detalleRoute, $s) }}#historial" class="solicitud-accion-btn solicitud-accion-btn--hist" title="Gestionar solicitud" aria-label="Abrir gestión de solicitud (detalle e historial en paneles)"><i class="fas fa-list" aria-hidden="true"></i></a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">No hay solicitudes para mostrar con los criterios indicados.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
    <div>
        @if ($solicitudes->total() > 0)
            Mostrando {{ $solicitudes->firstItem() }} a {{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }} registros
        @else
            Sin registros
        @endif
    </div>
    <div>{{ $solicitudes->links() }}</div>
</div>

@foreach ($solicitudes as $s)
    @include('panel.consultor.solicitudes._modal-detalle-solicitud', ['s' => $s])
@endforeach
@endsection
