@extends('layouts.app')

@section('title', 'Asociados de negocios — Consultor')

@push('styles')
<style>
    .table-legacy.asociados-legacy-table thead th {
        background: linear-gradient(180deg, #5a5d8a 0%, #4a4d78 100%) !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.8rem;
        border-color: rgba(255, 255, 255, 0.12) !important;
        white-space: nowrap;
        vertical-align: middle;
    }
    .table-legacy.asociados-legacy-table thead th a {
        color: #fff !important;
        text-decoration: none;
    }
    .table-legacy.asociados-legacy-table thead th a:hover { color: #e8e6ff !important; }
    .table-legacy.asociados-legacy-table thead th i { color: rgba(255, 255, 255, 0.75) !important; }
    .asociados-legacy-table tbody td { font-size: 0.875rem; vertical-align: middle; }
    .asociados-legacy-table .col-razon,
    .asociados-legacy-table .col-comercial { text-transform: uppercase; }
    .asociados-toolbar { gap: 0.75rem; }
    .asociados-toolbar__page-size,
    .asociados-toolbar__search {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.35rem;
    }
    .asociados-toolbar__search .input-group { min-width: 12rem; }
    .asociados-td-acciones { vertical-align: middle !important; }
    .asociados-acciones {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        flex-wrap: nowrap;
        line-height: 1;
    }
    .asociados-acciones__edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.85rem;
        height: 1.85rem;
        padding: 0;
        background: #fff;
        border: 1px solid #e6a317;
        border-radius: 4px;
        color: #e6a317;
        text-decoration: none;
        box-sizing: border-box;
    }
    .asociados-acciones__edit:hover {
        background: #fffdf5;
        color: #c78f0a;
        border-color: #c78f0a;
    }
    .asociados-acciones__edit i { font-size: 0.8rem; line-height: 1; }
    .asociados-acciones__delform { display: inline-flex; margin: 0; line-height: 0; }
    .asociados-acciones__delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.85rem;
        height: 1.85rem;
        padding: 0;
        background: #fff;
        border: 1px solid #dc3545;
        border-radius: 4px;
        color: #dc3545;
        box-sizing: border-box;
    }
    .asociados-acciones__delete:hover {
        background: #fff5f5;
        color: #b02a37;
        border-color: #b02a37;
    }
    .asociados-acciones__delete i { font-size: 0.75rem; line-height: 1; }
    .modal-asociados-legacy .modal-header.modal-asociados-legacy__bar {
        background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .modal-asociados-legacy .modal-footer.modal-asociados-legacy__footer {
        background: linear-gradient(180deg, #083060 0%, #06274a 100%) !important;
    }
    .modal-asociados-legacy .modal-footer .btn-outline-light {
        border-width: 2px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
    }
    .modal-asociados-legacy .modal-footer .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }
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
        return route('panel.consultor.asociados.index', array_merge($baseQuery, [
            'sort' => $col,
            'dir' => $nextDir,
        ]));
    };
    $sortIcon = function (string $col) use ($sort, $dir): string {
        if ($sort !== $col) {
            return 'fa-sort text-white-50';
        }
        return $dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    };
@endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
    <h1 class="fw-light mb-0" style="font-size:1.75rem;">Asociados de negocios</h1>
    @can('create', \App\Models\Proveedor::class)
        <a href="{{ route('panel.consultor.asociados.index', array_merge($baseQuery, ['open_modal' => 'crear'])) }}" class="btn btn-primary btn-sm text-uppercase fw-semibold px-3">Agregar asociado</a>
    @endcan
</div>
<p class="text-muted small mb-3">Alta, edición y baja (solo si no hay solicitudes ni usuarios vinculados).</p>

<form method="get" action="{{ route('panel.consultor.asociados.index') }}" class="mb-3">
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir" value="{{ $dir }}">
    <div class="d-flex flex-wrap align-items-center justify-content-between asociados-toolbar">
        <div class="asociados-toolbar__page-size text-muted small">
            <span>Mostrar</span>
            <select name="per_page" class="form-select form-select-sm" style="width: auto; min-width: 4.25rem; max-width: 5rem; padding-top: 0.2rem; padding-bottom: 0.2rem; flex: 0 0 auto;" onchange="this.form.submit()" aria-label="Registros por página">
                @foreach ([10, 25, 50, 100] as $n)
                    <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                @endforeach
            </select>
            <span>registros</span>
        </div>
        <div class="asociados-toolbar__search text-muted small">
            <label for="buscar_asociados" class="mb-0 text-nowrap">Buscar:</label>
            <div class="input-group input-group-sm">
                <input type="search" name="q" id="buscar_asociados" class="form-control" value="{{ $q }}" placeholder="NIT, razón social, comercial, ciudad…" autocomplete="off">
                <button type="submit" class="btn btn-outline-secondary">Buscar</button>
            </div>
        </div>
    </div>
</form>

<div class="table-responsive rounded-legacy bg-white shadow-sm">
    <table class="table table-legacy table-sm table-bordered table-hover table-striped align-middle mb-0 asociados-legacy-table">
        <thead>
        <tr>
            <th scope="col">
                <a href="{{ $sortLink('nit') }}">NIT <i class="fas {{ $sortIcon('nit') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('razon_social') }}">Razón social <i class="fas {{ $sortIcon('razon_social') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('comercial') }}">Comercial <i class="fas {{ $sortIcon('comercial') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('ciudad') }}">Ciudad <i class="fas {{ $sortIcon('ciudad') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('contacto') }}">Contacto <i class="fas {{ $sortIcon('contacto') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('cargo') }}">Cargo <i class="fas {{ $sortIcon('cargo') }} fa-xs"></i></a>
            </th>
            <th scope="col" class="text-center text-nowrap">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($proveedores as $p)
            <tr>
                <td>{{ $p->NIT_proveedor ?? '—' }}</td>
                <td class="col-razon">{{ $p->razon_social_proveedor }}</td>
                <td class="col-comercial">{{ $p->nombre_comercial }}</td>
                <td>{{ $p->ciudad_proveedor ?? '—' }}</td>
                <td>{{ $p->nombre_contacto_proveedor ?? '—' }}</td>
                <td>{{ $p->cargo_contacto_proveedor ?? '—' }}</td>
                <td class="text-center text-nowrap asociados-td-acciones">
                    <div class="asociados-acciones">
                        @can('update', $p)
                            <a href="{{ route('panel.consultor.asociados.index', array_merge($baseQuery, ['open_modal' => 'editar', 'edit_proveedor' => $p->id_proveedor])) }}" class="asociados-acciones__edit" title="Editar" aria-label="Editar"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                        @endcan
                        @can('delete', $p)
                            <form method="post" action="{{ route('panel.consultor.asociados.destroy', $p) }}" class="asociados-acciones__delform" onsubmit="return confirm('¿Eliminar este asociado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="asociados-acciones__delete" title="Eliminar" aria-label="Eliminar">
                                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">No hay asociados con los criterios indicados.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
    <div>
        @if ($proveedores->total() > 0)
            Mostrando {{ $proveedores->firstItem() }} a {{ $proveedores->lastItem() }} de {{ $proveedores->total() }} registros
        @else
            Sin registros
        @endif
    </div>
    <div>{{ $proveedores->links() }}</div>
</div>
@include('panel.consultor.asociados.partials.modals-asociados')
@endsection

@push('scripts')
<script>
(function () {
    const showC = @json($autoshowModalCrear ?? false);
    const showE = @json($autoshowModalEditar ?? false);
    document.addEventListener('DOMContentLoaded', function () {
        if (showC) {
            const el = document.getElementById('modalAsociadoCrear');
            if (el) new bootstrap.Modal(el).show();
        }
        if (showE) {
            const el = document.getElementById('modalAsociadoEditar');
            if (el) new bootstrap.Modal(el).show();
        }
    });
})();
</script>
@endpush
