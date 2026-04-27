@extends('layouts.app')

@section('title', 'Clientes — Consultor')

@push('styles')
<style>
    .table-legacy.clientes-legacy-table thead th {
        background: linear-gradient(180deg, #5a5d8a 0%, #4a4d78 100%) !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.8rem;
        border-color: rgba(255, 255, 255, 0.12) !important;
        white-space: nowrap;
        vertical-align: middle;
    }
    .table-legacy.clientes-legacy-table thead th a {
        color: #fff !important;
        text-decoration: none;
    }
    .table-legacy.clientes-legacy-table thead th a:hover { color: #e8e6ff !important; }
    .table-legacy.clientes-legacy-table thead th i { color: rgba(255, 255, 255, 0.75) !important; }
    .clientes-legacy-table tbody td { font-size: 0.875rem; vertical-align: middle; }
    .clientes-legacy-table .col-razon,
    .clientes-legacy-table .col-nombre { text-transform: uppercase; }
    .clientes-toolbar { gap: 0.75rem; }
    .clientes-toolbar__page-size,
    .clientes-toolbar__search {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.35rem;
    }
    .clientes-toolbar__search .input-group { min-width: 12rem; }
    /* Misma pauta que usuarios: cajita lápiz + switch */
    .clientes-td-acciones { vertical-align: middle !important; }
    .clientes-acciones {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        flex-wrap: nowrap;
        line-height: 1;
    }
    .clientes-acciones__edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 1.85rem;
        height: 1.85rem;
        padding: 0;
        margin: 0;
        background: #fff;
        border: 1px solid #e6a317;
        border-radius: 4px;
        color: #e6a317;
        text-decoration: none;
        box-sizing: border-box;
    }
    .clientes-acciones__edit:hover {
        background: #fffdf5;
        color: #c78f0a;
        border-color: #c78f0a;
    }
    .clientes-acciones__edit--muted { opacity: 0.45; }
    .clientes-acciones__edit i { font-size: 0.8rem; line-height: 1; }
    .clientes-acciones .form-switch .form-check-input { margin-top: 0; cursor: pointer; }
    .clientes-acciones form {
        display: inline-flex;
        align-items: center;
        margin: 0;
        line-height: 0;
    }
    .modal-clientes-legacy .modal-header.modal-clientes-legacy__bar {
        background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .modal-clientes-legacy .modal-footer.modal-clientes-legacy__footer {
        background: linear-gradient(180deg, #083060 0%, #06274a 100%) !important;
    }
    .modal-clientes-legacy .modal-footer .btn-outline-light {
        border-width: 2px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
    }
    .modal-clientes-legacy .modal-footer .btn-outline-light:hover {
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
        return route('panel.consultor.clientes.index', array_merge($baseQuery, [
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
    <h1 class="fw-light mb-0" style="font-size:1.75rem;">Listado de Clientes</h1>
    @can('create', \App\Models\Cliente::class)
        <a href="{{ route('panel.consultor.clientes.index', array_merge($baseQuery, ['open_modal' => 'crear'])) }}" class="btn btn-primary btn-sm text-uppercase fw-semibold px-3">Agregar cliente</a>
    @endcan
</div>
<p class="text-muted small mb-3">Alta, edición y activar/inactivar cliente (y usuarios vinculados al inactivar).</p>

<form method="get" action="{{ route('panel.consultor.clientes.index') }}" class="mb-3">
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir" value="{{ $dir }}">
    <div class="d-flex flex-wrap align-items-center justify-content-between clientes-toolbar">
        <div class="clientes-toolbar__page-size text-muted small">
            <span>Mostrar</span>
            <select name="per_page" class="form-select form-select-sm" style="width: auto; min-width: 4.25rem; max-width: 5rem; padding-top: 0.2rem; padding-bottom: 0.2rem; flex: 0 0 auto;" onchange="this.form.submit()" aria-label="Registros por página">
                @foreach ([10, 25, 50, 100] as $n)
                    <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                @endforeach
            </select>
            <span>registros</span>
        </div>
        <div class="clientes-toolbar__search text-muted small">
            <label for="buscar_clientes" class="mb-0 text-nowrap">Buscar:</label>
            <div class="input-group input-group-sm">
                <input type="search" name="q" id="buscar_clientes" class="form-control" value="{{ $q }}" placeholder="NIT, razón social, correo, ciudad…" autocomplete="off">
                <button type="submit" class="btn btn-outline-secondary">Buscar</button>
            </div>
        </div>
    </div>
</form>

<div class="table-responsive rounded-legacy bg-white shadow-sm">
    <table class="table table-legacy table-sm table-bordered table-hover table-striped align-middle mb-0 clientes-legacy-table">
        <thead>
        <tr>
            <th scope="col">
                <a href="{{ $sortLink('nit') }}">NIT <i class="fas {{ $sortIcon('nit') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('razon_social') }}">Razón social <i class="fas {{ $sortIcon('razon_social') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('direccion') }}">Dirección <i class="fas {{ $sortIcon('direccion') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('ciudad') }}">Ciudad <i class="fas {{ $sortIcon('ciudad') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('telefono') }}">Teléfono <i class="fas {{ $sortIcon('telefono') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('correo') }}">Correo <i class="fas {{ $sortIcon('correo') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('nombre') }}">Nombre <i class="fas {{ $sortIcon('nombre') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('cargo') }}">Cargo <i class="fas {{ $sortIcon('cargo') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('tipo') }}">Tipo cliente <i class="fas {{ $sortIcon('tipo') }} fa-xs"></i></a>
            </th>
            <th scope="col" class="text-center text-nowrap">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($clientes as $c)
            @php
                $activo = (int) ($c->activo ?? 0) === 1;
            @endphp
            <tr>
                <td>{{ $c->NIT ?? '—' }}</td>
                <td class="col-razon">{{ $c->razon_social }}</td>
                <td>{{ $c->direccion_cliente ?? '—' }}</td>
                <td>{{ $c->ciudad_cliente ?? '—' }}</td>
                <td class="text-nowrap">{{ $c->telefono_cliente ?? '—' }}</td>
                <td>{{ $c->correo_cliente ?? '—' }}</td>
                <td class="col-nombre">{{ $c->nombre ? mb_strtoupper($c->nombre, 'UTF-8') : '—' }}</td>
                <td>{{ $c->cargo ?? '—' }}</td>
                <td>{{ $c->tipo_cliente ?? '—' }}</td>
                <td class="text-center text-nowrap clientes-td-acciones">
                    <div class="clientes-acciones">
                        @can('update', $c)
                            <a href="{{ route('panel.consultor.clientes.index', array_merge($baseQuery, ['open_modal' => 'editar', 'edit_cliente' => $c->id_cliente])) }}" class="clientes-acciones__edit @if(! $activo) clientes-acciones__edit--muted @endif" title="Editar" aria-label="Editar"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                        @endcan
                        @can('toggleActivo', $c)
                            <form method="post" action="{{ route('panel.consultor.clientes.toggle-activo', $c) }}">
                                @csrf
                                @method('PATCH')
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" role="switch" @checked($activo) onchange="this.form.submit()" title="Activo / inactivo" aria-label="Cliente activo">
                                </div>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center text-muted py-4">No hay clientes con los criterios indicados.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
    <div>
        @if ($clientes->total() > 0)
            Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} registros
        @else
            Sin registros
        @endif
    </div>
    <div>{{ $clientes->links() }}</div>
</div>
@include('panel.consultor.clientes.partials.modals-clientes')
@endsection

@push('scripts')
<script>
(function () {
    const showC = @json($autoshowModalCrear ?? false);
    const showE = @json($autoshowModalEditar ?? false);
    document.addEventListener('DOMContentLoaded', function () {
        if (showC) {
            const el = document.getElementById('modalClienteCrear');
            if (el) new bootstrap.Modal(el).show();
        }
        if (showE) {
            const el = document.getElementById('modalClienteEditar');
            if (el) new bootstrap.Modal(el).show();
        }
    });
})();
</script>
@endpush
