@extends('layouts.app')

@php
    $badgeEstado = static function (string $estado): string {
        return match (true) {
            strcasecmp($estado, 'Nuevo') === 0 => 'bg-secondary text-white',
            strcasecmp($estado, 'En proceso') === 0 => 'bg-warning text-dark',
            default => 'bg-light text-dark border',
        };
    };
@endphp

@section('title', 'Informes de Solicitudes — Consultor')

@section('content')
    <h1 class="h4 fw-bold text-dark mb-3">Informes de Solicitudes</h1>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('panel.consultor.informes.index') }}" class="mb-0">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label small text-muted mb-0" for="desde">Fecha inicio</label>
                        <input
                            type="date"
                            name="desde"
                            id="desde"
                            class="form-control"
                            value="{{ $filtros['desde'] ?? '' }}"
                            lang="es"
                        >
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label small text-muted mb-0" for="hasta">Fecha fin</label>
                        <input
                            type="date"
                            name="hasta"
                            id="hasta"
                            class="form-control"
                            value="{{ $filtros['hasta'] ?? '' }}"
                            lang="es"
                        >
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label small text-muted mb-0" for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="">todos</option>
                            @foreach (['Nuevo', 'En proceso', 'Completado', 'Cancelado', 'Registrado'] as $e)
                                <option value="{{ $e }}" @selected(($filtros['estado'] ?? '') === $e)>{{ $e }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label small text-muted mb-0" for="servicio_id">Servicio</label>
                        <select name="servicio_id" id="servicio_id" class="form-select">
                            <option value="">todos</option>
                            @foreach ($servicios as $srv)
                                <option value="{{ $srv->id_servicio }}" @selected((string) ($filtros['servicio_id'] ?? '') === (string) $srv->id_servicio)>
                                    {{ $srv->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary text-uppercase">
                            <i class="fas fa-search me-1" aria-hidden="true"></i> Filtrar
                        </button>
                        <a
                            href="{{ route('panel.consultor.informes.export', request()->query()) }}"
                            class="btn btn-success text-uppercase"
                        >
                            <i class="fas fa-file-excel me-1" aria-hidden="true"></i> Exportar a Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive rounded-legacy bg-white shadow-sm">
        <table class="table table-legacy table-sm table-bordered table-hover table-striped text-start align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>Cliente</th>
                <th>Documento</th>
                <th>Nombres</th>
                <th>Fecha creación</th>
                <th>Estado</th>
                <th>Servicio</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($solicitudes as $s)
                <tr>
                    <td>{{ $s->creador?->cliente?->razon_social ?? '—' }}</td>
                    <td>{{ trim(($s->tipo_identificacion ?? '').' '.($s->numero_documento ?? '')) ?: '—' }}</td>
                    <td class="text-uppercase">{{ trim(($s->nombres ?? '').' '.($s->apellidos ?? '')) ?: '—' }}</td>
                    <td class="text-nowrap">{{ $s->fecha_creacion?->format('d/m/Y, H:i') ?? '—' }}</td>
                    <td>
                        <span class="badge rounded-pill {{ $badgeEstado((string) ($s->estado ?? '')) }}">{{ $s->estado ?? '—' }}</span>
                    </td>
                    <td>{{ $s->labelServiciosContratados() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No hay resultados con los filtros actuales.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="text-muted small">
            @if ($solicitudes->total() > 0)
                Mostrando {{ $solicitudes->firstItem() }} a {{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }} resultados
            @else
                Sin resultados
            @endif
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="get" class="d-flex align-items-center gap-1">
                @foreach (request()->except('per_page') as $k => $v)
                    @if (is_array($v))
                        @foreach ($v as $vv)
                            <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <label class="small text-muted mb-0" for="per_page_sel">Mostrar</label>
                <select name="per_page" id="per_page_sel" class="form-select form-select-sm" style="width: auto" onchange="this.form.submit()">
                    @foreach ([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                    @endforeach
                </select>
            </form>
            {{ $solicitudes->withQueryString()->links() }}
        </div>
    </div>
@endsection
