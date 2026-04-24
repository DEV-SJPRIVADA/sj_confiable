@extends('layouts.app')

@section('title', 'Usuarios — Consultor')

@section('content')
    <div class="header-container-usuarios d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="fw-light">Listado de Usuarios</h1>
        @can('create', \App\Models\Usuario::class)
            <a href="{{ route('panel.consultor.usuarios.create') }}" class="btn btn-primary btn-sm">Nuevo usuario</a>
        @endcan
    </div>
    <p class="text-muted small mb-3">Alta y edición con reglas del legado (admin no asigna roles SJ 2/3 ni edita SuperAdmin).</p>
    <div class="table-responsive rounded-legacy bg-white">
        <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Cliente</th>
                <th>Asociado</th>
                <th>Activo</th>
                <th>Ciudad</th>
                <th class="text-nowrap">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($usuarios as $u)
                <tr>
                    <td>{{ $u->id_usuario }}</td>
                    <td>{{ $u->usuario }}</td>
                    <td>{{ $u->rol?->nombre ?? '—' }}</td>
                    <td>{{ $u->cliente?->razon_social ?? '—' }}</td>
                    <td>{{ $u->proveedor?->nombre_comercial ?? '—' }}</td>
                    <td>{{ (int) $u->activo === 1 ? 'Sí' : 'No' }}</td>
                    <td>{{ $u->ciudad ?? '—' }}</td>
                    <td class="text-nowrap">
                        @can('update', $u)
                            <a href="{{ route('panel.consultor.usuarios.edit', $u) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $usuarios->links() }}
    </div>
@endsection
