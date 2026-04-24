@extends('layouts.app')

@php
    $nombreServicios = function ($s) {
        if ($s->serviciosPivote->isNotEmpty()) {
            return $s->serviciosPivote->pluck('nombre')->implode(', ');
        }
        return $s->servicio?->nombre ?? '—';
    };
@endphp

@section('title', 'Informes de solicitudes — Consultor')

@section('content')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 text-primary mb-3"><i class="fas fa-filter me-2"></i>Informes de solicitudes</h2>
            <form method="get" action="{{ route('panel.consultor.informes.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small text-muted mb-0" for="desde">Desde</label>
                    <input type="date" name="desde" id="desde" class="form-control" value="{{ $filtros['desde'] ?? '' }}">
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small text-muted mb-0" for="hasta">Hasta</label>
                    <input type="date" name="hasta" id="hasta" class="form-control" value="{{ $filtros['hasta'] ?? '' }}">
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small text-muted mb-0" for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        @foreach (['Nuevo', 'En proceso', 'Completado', 'Cancelado', 'Registrado'] as $e)
                            <option value="{{ $e }}" @selected(($filtros['estado'] ?? '') === $e)>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-3">
                    <label class="form-label small text-muted mb-0" for="servicio_id">Servicio</label>
                    <select name="servicio_id" id="servicio_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($servicios as $srv)
                            <option value="{{ $srv->id_servicio }}" @selected((string) ($filtros['servicio_id'] ?? '') === (string) $srv->id_servicio)>
                                {{ $srv->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-3 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('panel.consultor.informes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive rounded-legacy bg-white">
        <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Evaluado</th>
                <th>Documento</th>
                <th>Usuario</th>
                <th>Cliente</th>
                <th>Servicio</th>
                <th>Estado</th>
                <th>Creación</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($solicitudes as $s)
                <tr>
                    <td>
                        <a href="{{ route('panel.consultor.solicitudes.show', $s) }}" class="text-decoration-none" style="color: #0d6efd;">{{ $s->id }}</a>
                    </td>
                    <td>{{ $s->nombres }} {{ $s->apellidos }}</td>
                    <td>{{ $s->tipo_identificacion }} {{ $s->numero_documento }}</td>
                    <td>{{ $s->creador?->usuario ?? '—' }}</td>
                    <td>{{ $s->creador?->cliente?->razon_social ?? '—' }}</td>
                    <td>{{ $nombreServicios($s) }}</td>
                    <td>{{ $s->estado }}</td>
                    <td class="text-nowrap">{{ $s->fecha_creacion?->format('Y-m-d H:i') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No hay resultados con los filtros actuales.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $solicitudes->links() }}
    </div>
@endsection
