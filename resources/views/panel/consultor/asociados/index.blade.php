@extends('layouts.app')

@section('title', 'Asociados de negocios — Consultor')

@section('content')
    <div class="header-container-proveedores d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="fw-light">Asociados de negocios</h1>
        @can('create', \App\Models\Proveedor::class)
            <a href="{{ route('panel.consultor.asociados.create') }}" class="btn btn-primary btn-sm">Nuevo asociado</a>
        @endcan
    </div>
    <p class="text-muted small mb-3">CRUD de asociados. La eliminación solo se permite si no hay solicitudes ni usuarios vinculados.</p>
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
                <th class="text-nowrap">Acciones</th>
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
                    <td class="text-nowrap">
                        @can('update', $p)
                            <a href="{{ route('panel.consultor.asociados.edit', $p) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        @endcan
                        @can('delete', $p)
                            <form method="post" action="{{ route('panel.consultor.asociados.destroy', $p) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este asociado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $proveedores->links() }}
    </div>
@endsection
