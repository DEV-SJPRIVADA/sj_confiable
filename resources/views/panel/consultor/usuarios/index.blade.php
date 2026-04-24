@extends('layouts.app')

@section('title', 'Usuarios — Consultor')

@section('content')
    <div class="header-container-usuarios d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="fw-light">Listado de Usuarios</h1>
    </div>
    <p class="text-muted small mb-3">Vista de consulta. Los flujos de crear/editar (modales del legado) se integrarán en una fase posterior.</p>
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
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $usuarios->links() }}
    </div>
@endsection
