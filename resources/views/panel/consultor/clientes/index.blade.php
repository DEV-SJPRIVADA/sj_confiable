@extends('layouts.app')

@section('title', 'Clientes — Consultor')

@section('content')
    <div class="header-container-clientes d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="fw-light">Listado de Clientes</h1>
    </div>
    <p class="text-muted small mb-3">Vista de consulta. Alta y edición (modales del legado) se portarán después.</p>
    <div class="table-responsive rounded-legacy bg-white">
        <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Razón social</th>
                <th>NIT</th>
                <th>Ciudad</th>
                <th>Correo</th>
                <th>Tipo</th>
                <th>Activo</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($clientes as $c)
                <tr>
                    <td>{{ $c->id_cliente }}</td>
                    <td>{{ $c->razon_social }}</td>
                    <td>{{ $c->NIT ?? '—' }}</td>
                    <td>{{ $c->ciudad_cliente ?? '—' }}</td>
                    <td>{{ $c->correo_cliente ?? '—' }}</td>
                    <td>{{ $c->tipo_cliente ?? '—' }}</td>
                    <td>{{ (int) ($c->activo ?? 0) === 1 ? 'Sí' : 'No' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $clientes->links() }}
    </div>
@endsection
