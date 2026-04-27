@extends('layouts.app')

@section('title', 'Usuarios — Consultor')

@push('styles')
<style>
    /* Gana a .table-legacy thead th de panel-tables-laravel.css (#e9ecef) */
    .table-legacy.usuarios-legacy-table thead th {
        background: linear-gradient(180deg, #5a5d8a 0%, #4a4d78 100%) !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.8rem;
        border-color: rgba(255, 255, 255, 0.12) !important;
        white-space: nowrap;
        vertical-align: middle;
    }
    .table-legacy.usuarios-legacy-table thead th a {
        color: #fff !important;
        text-decoration: none;
    }
    .table-legacy.usuarios-legacy-table thead th a:hover { color: #e8e6ff !important; }
    .table-legacy.usuarios-legacy-table thead th i { color: rgba(255, 255, 255, 0.75) !important; }
    .usuarios-legacy-table tbody td { font-size: 0.875rem; vertical-align: middle; }
    .usuarios-legacy-table .col-nombre,
    .usuarios-legacy-table .col-cliente { text-transform: uppercase; }
    .usuarios-toolbar { gap: 0.75rem; }
    .usuarios-toolbar__page-size,
    .usuarios-toolbar__search {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.35rem;
    }
    .usuarios-toolbar__search .input-group { min-width: 12rem; }
    /* Modales usuario: barra azul oscuro + degradado (referencia legado) */
    .modal-usuarios-legacy .modal-header.modal-usuarios-legacy__bar {
        background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .modal-usuarios-legacy .modal-footer.modal-usuarios-legacy__footer {
        background: linear-gradient(180deg, #083060 0%, #06274a 100%) !important;
    }
    .modal-usuarios-legacy .modal-footer .btn-outline-light {
        border-width: 2px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
    }
    .modal-usuarios-legacy .modal-footer .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }
    .us-modal-form-switch .form-check-input {
        width: 2.35rem;
        height: 1.2rem;
        cursor: pointer;
    }
    .us-modal-form-switch .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0a58ca;
    }
    /* Columna Acciones: cajita lápiz + switch alineados (referencia legado) */
    .usuarios-td-acciones {
        vertical-align: middle !important;
    }
    .usuarios-acciones {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        flex-wrap: nowrap;
        line-height: 1;
    }
    .usuarios-acciones__edit {
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
    .usuarios-acciones__edit:hover {
        background: #fffdf5;
        color: #c78f0a;
        border-color: #c78f0a;
    }
    .usuarios-acciones__edit--muted {
        opacity: 0.45;
    }
    .usuarios-acciones__edit i {
        font-size: 0.8rem;
        line-height: 1;
    }
    .usuarios-acciones .form-switch .form-check-input {
        margin-top: 0;
        cursor: pointer;
    }
    .usuarios-acciones form {
        display: inline-flex;
        align-items: center;
        margin: 0;
        line-height: 0;
    }
</style>
@endpush

@section('content')
@php
    /** @var int $perPage */
    /** @var string $q */
    /** @var string $sort */
    /** @var string $dir */
    $baseQuery = array_filter([
        'per_page' => $perPage,
        'q' => $q !== '' ? $q : null,
        'sort' => $sort,
        'dir' => $dir,
    ], fn ($v) => $v !== null && $v !== '');
    $sortLink = function (string $col) use ($baseQuery, $sort, $dir): string {
        $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';

        return route('panel.consultor.usuarios.index', array_merge($baseQuery, [
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
    <h1 class="fw-light mb-0" style="font-size:1.75rem;">Listado de Usuarios</h1>
    @can('create', \App\Models\Usuario::class)
        <a href="{{ route('panel.consultor.usuarios.index', array_merge($baseQuery, ['open_modal' => 'crear'])) }}" class="btn btn-primary btn-sm text-uppercase fw-semibold px-3">Agregar usuario</a>
    @endcan
</div>
<p class="text-muted small mb-3">Alta y edición con reglas del legado (admin no asigna roles SJ 2/3 ni edita SuperAdmin).</p>

<form method="get" action="{{ route('panel.consultor.usuarios.index') }}" class="mb-3">
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir" value="{{ $dir }}">
    <div class="d-flex flex-wrap align-items-center justify-content-between usuarios-toolbar">
        <div class="usuarios-toolbar__page-size text-muted small">
            <span>Mostrar</span>
            <select name="per_page" class="form-select form-select-sm" style="width: auto; min-width: 4.25rem; max-width: 5rem; padding-top: 0.2rem; padding-bottom: 0.2rem; flex: 0 0 auto;" onchange="this.form.submit()" aria-label="Registros por página">
                @foreach ([10, 25, 50, 100] as $n)
                    <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                @endforeach
            </select>
            <span>registros</span>
        </div>
        <div class="usuarios-toolbar__search text-muted small">
            <label for="buscar_usuarios" class="mb-0 text-nowrap">Buscar:</label>
            <div class="input-group input-group-sm">
                <input type="search" name="q" id="buscar_usuarios" class="form-control" value="{{ $q }}" placeholder="Usuario, nombre, correo, cliente…" autocomplete="off">
                <button type="submit" class="btn btn-outline-secondary">Buscar</button>
            </div>
        </div>
    </div>
</form>

<div class="table-responsive rounded-legacy bg-white shadow-sm">
    <table class="table table-legacy table-sm table-bordered table-hover table-striped align-middle mb-0 usuarios-legacy-table">
        <thead>
        <tr>
            <th scope="col">
                <a href="{{ $sortLink('usuario') }}">Usuario <i class="fas {{ $sortIcon('usuario') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('id_rol') }}">Rol <i class="fas {{ $sortIcon('id_rol') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('ciudad') }}">Ciudad <i class="fas {{ $sortIcon('ciudad') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('nombre') }}">Nombre <i class="fas {{ $sortIcon('nombre') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('correo') }}">Correo <i class="fas {{ $sortIcon('correo') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('celular') }}">Celular <i class="fas {{ $sortIcon('celular') }} fa-xs"></i></a>
            </th>
            <th scope="col">
                <a href="{{ $sortLink('cliente') }}">Cliente <i class="fas {{ $sortIcon('cliente') }} fa-xs"></i></a>
            </th>
            <th scope="col" class="text-center text-nowrap">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($usuarios as $u)
            @php
                $p = $u->persona;
                $nombreCompleto = $p
                    ? mb_strtoupper(trim($p->nombre.' '.$p->paterno.' '.($p->materno ?? '')), 'UTF-8')
                    : '—';
                $org = $u->cliente?->razon_social ?? $u->proveedor?->nombre_comercial ?? '—';
                $orgDisplay = $org !== '—' ? mb_strtoupper($org, 'UTF-8') : '—';
            @endphp
            <tr>
                <td>{{ $u->usuario }}</td>
                <td>{{ $u->rol ? mb_strtolower($u->rol->nombre, 'UTF-8') : '—' }}</td>
                <td>{{ $u->ciudad ?? '—' }}</td>
                <td class="col-nombre">{{ $nombreCompleto }}</td>
                <td>{{ $p?->correo ?? '—' }}</td>
                <td class="text-nowrap">{{ $p?->celular ?? '—' }}</td>
                <td class="col-cliente">{{ $orgDisplay }}</td>
                <td class="text-center text-nowrap usuarios-td-acciones">
                    <div class="usuarios-acciones">
                        @can('update', $u)
                            <a href="{{ route('panel.consultor.usuarios.index', array_merge($baseQuery, ['open_modal' => 'editar', 'edit_usuario' => $u->id_usuario])) }}" class="usuarios-acciones__edit @if(! $u->isActive()) usuarios-acciones__edit--muted @endif" title="Editar" aria-label="Editar"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                        @endcan
                        @can('toggleActivo', $u)
                            <form method="post" action="{{ route('panel.consultor.usuarios.toggle-activo', $u) }}">
                                @csrf
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" role="switch" @checked($u->isActive()) onchange="this.form.submit()" title="Activo / inactivo" aria-label="Activo / inactivo">
                                </div>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">No hay usuarios con los criterios indicados.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
    <div>
        @if ($usuarios->total() > 0)
            Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} registros
        @else
            Sin registros
        @endif
    </div>
    <div>{{ $usuarios->links() }}</div>
</div>

@include('panel.consultor.usuarios.partials.modals-usuarios')
@endsection

@push('scripts')
<script>
(function () {
    const prov = @json((int) \App\Domain\Enums\UserRole::Proveedor->value);
    const showC = @json($autoshowModalCrear);
    const showE = @json($autoshowModalEditar);

    function initRol(modal) {
        const sel = modal.querySelector('select.id-rol-select');
        if (!sel) return;
        const idWC = sel.getAttribute('data-wrap-cliente');
        const idWP = sel.getAttribute('data-wrap-proveedor');
        const idC = sel.getAttribute('data-id-cliente');
        const idP = sel.getAttribute('data-id-proveedor');
        if (!idWC || !idWP || !idC || !idP) return;
        const wC = document.getElementById(idWC);
        const wP = document.getElementById(idWP);
        const c = document.getElementById(idC);
        const p = document.getElementById(idP);
        if (!wC || !wP || !c || !p) return;
        function apply() {
            const v = parseInt(sel.value, 10) || 0;
            if (v === prov) {
                wC.classList.add('d-none');
                c.removeAttribute('required');
                wP.classList.remove('d-none');
                p.setAttribute('required', 'required');
            } else {
                wP.classList.add('d-none');
                p.removeAttribute('required');
                wC.classList.remove('d-none');
                c.setAttribute('required', 'required');
            }
        }
        sel.addEventListener('change', apply);
        apply();
    }

    function initPwInput(input) {
        const opt = input.getAttribute('data-pw-optional') === '1';
        const wrap = input.closest('.position-relative');
        const rulesUl = wrap ? wrap.querySelector('ul[id$="_pw_rules"]') : null;
        const ig = input.closest('.input-group');
        const toggleBtn = ig ? ig.querySelector('button[data-toggle-pw]') : null;
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const t = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', t);
            });
        }
        function hasSpecial(s) {
            return /[^0-9A-Za-záéíóúñÁÉÍÓÚÑ]/.test(s);
        }
        function runRules() {
            if (!rulesUl) return;
            const v = input.value;
            if (opt && v.length === 0) {
                rulesUl.querySelectorAll('.ru-len, .ru-up, .ru-lo, .ru-num, .ru-spl').forEach(function (i) { i.classList.add('opacity-0'); });
                return;
            }
            const lenIcon = rulesUl.querySelector('.ru-len');
            const upIcon = rulesUl.querySelector('.ru-up');
            const loIcon = rulesUl.querySelector('.ru-lo');
            const numIcon = rulesUl.querySelector('.ru-num');
            const splIcon = rulesUl.querySelector('.ru-spl');
            if (lenIcon) lenIcon.classList.toggle('opacity-0', v.length < 8 || v.length > 15);
            if (upIcon) upIcon.classList.toggle('opacity-0', !/[A-ZÁÉÍÓÚÑ]/.test(v));
            if (loIcon) loIcon.classList.toggle('opacity-0', !/[a-záéíóúñ]/.test(v));
            if (numIcon) numIcon.classList.toggle('opacity-0', !/\d/.test(v));
            if (splIcon) splIcon.classList.toggle('opacity-0', !hasSpecial(v));
        }
        input.addEventListener('input', runRules);
        runRules();
    }

    function initPasswordsIn(modal) {
        modal.querySelectorAll('input[name="password"]').forEach(initPwInput);
    }

    document.addEventListener('DOMContentLoaded', function () {
        ['modalUsuarioCrear', 'modalUsuarioEditar'].forEach(function (id) {
            const m = document.getElementById(id);
            if (!m) return;
            initRol(m);
            initPasswordsIn(m);
        });
        if (showC) {
            const elC = document.getElementById('modalUsuarioCrear');
            if (elC) new bootstrap.Modal(elC).show();
        }
        if (showE) {
            const elE = document.getElementById('modalUsuarioEditar');
            if (elE) new bootstrap.Modal(elE).show();
        }
    });
})();
</script>
@endpush
