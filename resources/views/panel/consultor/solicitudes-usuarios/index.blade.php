@extends('layouts.app')

@section('title', 'Solicitudes de gestión de usuarios — Consultor')

@push('styles')
<style>
    .table-legacy.sol-usu-legacy-table thead th {
        background: #e9ecef !important;
        color: #212529 !important;
        font-weight: 600;
        font-size: 0.8rem;
        border-color: #dee2e6 !important;
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
    }
    .table-legacy.sol-usu-legacy-table thead th a {
        color: #0d3a66 !important;
        text-decoration: none;
    }
    .table-legacy.sol-usu-legacy-table thead th a:hover { color: #0a58ca !important; }
    .table-legacy.sol-usu-legacy-table thead th i { color: #6c757d !important; }
    .sol-usu-legacy-table tbody td { font-size: 0.875rem; vertical-align: middle; }
    .sol-usu-legacy-table .text-cliente { text-transform: uppercase; }
    .sol-usu-toolbar { gap: 0.75rem; }
    .sol-usu-toolbar__page-size,
    .sol-usu-toolbar__search {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.35rem;
    }
    .sol-usu-toolbar__search .input-group { min-width: 12rem; }
    .modal-sol-usu-legacy .modal-header { background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%) !important; color: #fff; border: none; }
    .modal-sol-usu-legacy .btn-close { filter: invert(1); }
</style>
@endpush

@section('content')
@php
    $baseQuery = array_filter([
        'per_page' => $perPage,
        'q' => $q !== '' ? $q : null,
        'sort' => $sort,
        'dir' => $dir,
    ], fn ($v) => $v !== null && $v !== '');
    $sortLink = function (string $col) use ($baseQuery, $sort, $dir): string {
        $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';

        return route('panel.consultor.solicitudes-usuarios.index', array_merge($baseQuery, [
            'sort' => $col,
            'dir' => $nextDir,
        ]));
    };
    $sortIcon = function (string $col) use ($sort, $dir): string {
        if ($sort !== $col) {
            return 'fa-sort text-muted';
        }

        return $dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    };
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
    <h1 class="fw-light mb-0" style="font-size:1.75rem;">Solicitudes de Gestión de Usuarios</h1>
</div>
<p class="text-muted small mb-3">Peticiones de clientes. Las pendientes pueden aprobarse o rechazarse; el comentario es obligatorio al responder.</p>

<form method="get" action="{{ route('panel.consultor.solicitudes-usuarios.index') }}" class="mb-3">
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir" value="{{ $dir }}">
    <div class="d-flex flex-wrap align-items-center justify-content-between sol-usu-toolbar">
        <div class="sol-usu-toolbar__page-size text-muted small">
            <span>Mostrar</span>
            <select name="per_page" class="form-select form-select-sm" style="width: auto; min-width: 4.25rem; max-width: 5rem; padding-top: 0.2rem; padding-bottom: 0.2rem; flex: 0 0 auto;" onchange="this.form.submit()" aria-label="Registros por página">
                @foreach ([10, 25, 50, 100] as $n)
                    <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                @endforeach
            </select>
            <span>registros</span>
        </div>
        <div class="sol-usu-toolbar__search text-muted small">
            <label for="buscar_sol_usu" class="mb-0 text-nowrap">Buscar:</label>
            <div class="input-group input-group-sm">
                <input type="search" name="q" id="buscar_sol_usu" class="form-control" value="{{ $q }}" placeholder="ID, cliente, solicitante, tipo, estado…" autocomplete="off">
                <button type="submit" class="btn btn-outline-secondary">Buscar</button>
            </div>
        </div>
    </div>
</form>

<div class="table-responsive rounded-legacy bg-white shadow-sm">
    <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0 sol-usu-legacy-table">
        <thead>
        <tr>
            <th scope="col" class="text-nowrap text-center">
                <a href="{{ $sortLink('id') }}">ID <i class="fas {{ $sortIcon('id') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-center">
                <a href="{{ $sortLink('cliente') }}">Cliente <i class="fas {{ $sortIcon('cliente') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-center">
                <a href="{{ $sortLink('solicitante') }}">Solicitante <i class="fas {{ $sortIcon('solicitante') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-center text-nowrap">
                <a href="{{ $sortLink('tipo') }}">Tipo <i class="fas {{ $sortIcon('tipo') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-center text-nowrap">
                <a href="{{ $sortLink('estado') }}">Estado <i class="fas {{ $sortIcon('estado') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-center text-nowrap">
                <a href="{{ $sortLink('fecha') }}">Fecha solicitud <i class="fas {{ $sortIcon('fecha') }} fa-xs" aria-hidden="true"></i></a>
            </th>
            <th scope="col" class="text-center text-nowrap" style="min-width: 7rem;">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($solicitudes as $s)
            <tr>
                <td class="text-center text-nowrap">{{ $s->id_solicitud }}</td>
                <td class="text-cliente text-break">{{ $s->cliente?->razon_social ?? '—' }}</td>
                <td class="text-break">{{ $s->solicitante?->usuario ?? '—' }}</td>
                <td class="text-center text-nowrap">{{ $s->tipo ?? '—' }}</td>
                <td class="text-center text-nowrap">{{ $s->estado ?? '—' }}</td>
                <td class="text-center text-nowrap">{{ $s->fecha_solicitud?->format('d/m/Y H:i') ?? '—' }}</td>
                <td class="text-center text-nowrap">
                    @if ($s->estado === 'Pendiente')
                        @can('respond', $s)
                            <button
                                type="button"
                                class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalResponderSolicitudUsuario"
                                data-action="{{ route('panel.consultor.solicitudes-usuarios.responder', $s) }}"
                                data-solicitud-id="{{ $s->id_solicitud }}"
                            >Gestionar</button>
                        @else
                            <span class="text-muted small">—</span>
                        @endcan
                    @else
                        <span class="text-muted small">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">No hay datos disponibles en la tabla</td>
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
            Mostrando 0 a 0 de 0 registros
        @endif
    </div>
    <div>{{ $solicitudes->links() }}</div>
</div>

<div class="modal fade modal-sol-usu-legacy" id="modalResponderSolicitudUsuario" tabindex="-1" aria-labelledby="modalResponderSolicitudUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="modalResponderSolicitudUsuarioLabel">Responder solicitud <span class="text-white-50 small fw-normal" id="modalSolUsuId"></span></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="post" action="#" id="formResponderSolicitudUsuario">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="modal_estado_solicitud">Decisión</label>
                        <select name="estado" id="modal_estado_solicitud" class="form-select" required>
                            <option value="" disabled selected>— Seleccione —</option>
                            <option value="Aprobada">Aprobar</option>
                            <option value="Rechazada">Rechazar</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="modal_comentario_solicitud">Comentario</label>
                        <textarea name="comentario" id="modal_comentario_solicitud" class="form-control" rows="4" required maxlength="2000" placeholder="Motivo o detalle de la respuesta"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Enviar respuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var m = document.getElementById('modalResponderSolicitudUsuario');
    if (!m) return;
    m.addEventListener('show.bs.modal', function (ev) {
        var btn = ev.relatedTarget;
        if (!btn) return;
        var action = btn.getAttribute('data-action');
        var sid = btn.getAttribute('data-solicitud-id');
        var f = document.getElementById('formResponderSolicitudUsuario');
        if (f && action) f.setAttribute('action', action);
        var sp = document.getElementById('modalSolUsuId');
        if (sp) sp.textContent = sid ? ' #' + sid : '';
        var sel = document.getElementById('modal_estado_solicitud');
        if (sel) { sel.selectedIndex = 0; }
        var ta = document.getElementById('modal_comentario_solicitud');
        if (ta) { ta.value = ''; }
    });
})();
</script>
@endpush
