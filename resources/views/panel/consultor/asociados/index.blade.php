@extends('layouts.app')

@section('title', 'Asociados de negocios — Consultor')

@section('content')
    <div class="header-container-proveedores d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="fw-light">Asociados de negocios</h1>
    </div>
    <p class="text-muted small mb-3">Vista de consulta. Registro y edición de asociados (flujo del legado) se portará después.</p>
    <div class="table-responsive rounded-legacy bg-white">
        <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Razón social</th>
                <th>Nombre comercial</th>
                <th>Ciudad</th>
                <th>Correo</th>
                <th>Contacto</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($proveedores as $p)
                <tr>
                    <td>{{ $p->id_proveedor }}</td>
                    <td>{{ $p->razon_social_proveedor }}</td>
                    <td>{{ $p->nombre_comercial }}</td>
                    <td>{{ $p->ciudad_proveedor ?? '—' }}</td>
                    <td>{{ $p->correo_proveedor ?? '—' }}</td>
                    <td>{{ $p->nombre_contacto_proveedor ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $proveedores->links() }}
    </div>
@endsection
