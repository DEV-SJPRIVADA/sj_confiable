@extends('layouts.app')

@php
    $f = $filtros ?? [];
    $detalleRoute = 'panel.cliente.solicitudes.show';
    $ciudadFila = function ($s) {
        $a = $s->ciudad_solicitud_servicio ?? '';
        if (is_string($a) && trim($a) !== '') {
            return $a;
        }
        $b = $s->ciudad_residencia_evaluado ?? '';
        if (is_string($b) && trim($b) !== '') {
            return $b;
        }
        return '';
    };
    $rowClassSolicitud = function ($s) {
        $e = mb_strtolower(trim((string) $s->estado));
        if (str_contains($e, 'cancel')) {
            return ' table-danger';
        }
        if (str_contains($e, 'proceso')) {
            return ' table-warning';
        }
        return '';
    };
@endphp

@section('title', 'Inicio — Cliente')

@push('styles')
<style>
    @include('panel.partials._styles-cliente-acciones-solicitud')
    .panel-cliente-home { background: #f1f3f5; }
    .panel-cliente-home .dashboard-filters { background: #e9ecef; border-radius: 0.35rem; }
    .panel-cliente-home .widget-title { color: #0d6efd; font-weight: 600; font-size: 0.95rem; }
    .panel-cliente-home .widget-title i { opacity: 0.85; }
    .panel-cliente-home .chart-box { min-height: 280px; position: relative; }
    .panel-cliente-home .bienvenida-inicio h1 { font-size: 1.35rem; font-weight: 700; color: #212529; }
    .panel-cliente-home .solicitud-tabla th { font-size: 0.8rem; }
    .panel-cliente-home .table-solicitud-id a { color: #0d6efd; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="panel-cliente-home">
    <div class="bienvenida-inicio text-center mb-4">
        <h1 class="mb-1">Bienvenido, {{ $bienvenidaNombre }}</h1>
        <p class="text-muted small mb-0">Estamos encantados de verte de nuevo.</p>
    </div>

    <form method="get" action="{{ route('panel.cliente.inicio') }}" class="dashboard-filters p-3 mb-3" id="formFiltrosInicioCliente">
        <div class="row g-2 small align-items-end">
            <div class="col-12 col-md-6 col-lg-2">
                <label class="form-label mb-0 text-muted">Estado</label>
                <select name="estado" class="form-select form-select-sm" aria-label="Filtrar por estado">
                    <option value="">Todos</option>
                    @foreach ($estados as $e)
                        <option value="{{ $e }}" @selected(($f['estado'] ?? '') === $e)>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label mb-0 text-muted">Fecha inicio</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="{{ $f['desde'] ?? '' }}">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label mb-0 text-muted">Fecha fin</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="{{ $f['hasta'] ?? '' }}">
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label mb-0 text-muted" for="q_inicio_cliente">Buscar</label>
                <input type="search" name="q" id="q_inicio_cliente" class="form-control form-control-sm" value="{{ $q ?? '' }}" placeholder="ID, evaluado, documento, ciudad, estado, usuario…" autocomplete="off">
            </div>
            <div class="col-6 col-md-3 col-lg-1">
                <label class="form-label mb-0 text-muted" for="per_page_inicio">Mostrar</label>
                <select name="per_page" id="per_page_inicio" class="form-select form-select-sm" aria-label="Registros por página">
                    @foreach ([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" @selected((int) request('per_page', 10) === $n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-lg-2 d-flex flex-wrap gap-1">
                <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
                <a href="{{ route('panel.cliente.inicio') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="widget-title mb-2"><i class="fas fa-chart-pie me-2" aria-hidden="true"></i>Solicitudes por estado</h2>
                    <div class="chart-box" style="max-height: 300px;">
                        <canvas id="chartEstados" aria-label="Solicitudes por estado"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="widget-title mb-2"><i class="fas fa-chart-bar me-2" aria-hidden="true"></i>Solicitudes por servicio</h2>
                    <div class="chart-box" style="max-height: 300px;">
                        <canvas id="chartServicios" aria-label="Solicitudes por servicio"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="widget-title mb-2"><i class="fas fa-map-marker-alt me-2" aria-hidden="true"></i>Solicitudes por ciudad</h2>
                    <div class="chart-box" style="max-height: 300px;">
                        <canvas id="chartCiudades" aria-label="Solicitudes por ciudad"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="widget-title mb-2"><i class="fas fa-chart-line me-2" aria-hidden="true"></i>Solicitudes por fecha</h2>
                    <div class="chart-box" style="max-height: 300px;">
                        <canvas id="chartEvolucion" aria-label="Evolución de solicitudes"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-0">
            <h2 class="widget-title p-3 pb-0 mb-2"><i class="fas fa-th me-2" aria-hidden="true"></i>Solicitudes de confiabilidad</h2>
            <div class="table-responsive">
                <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0 solicitud-tabla">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Servicio</th>
                            <th>Ciudad</th>
                            <th>Estado</th>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th class="text-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($solicitudes as $s)
                        @php $ciudad = $ciudadFila($s); @endphp
                        <tr class="{{ $rowClassSolicitud($s) }}">
                            <td class="text-nowrap table-solicitud-id">
                                <a href="{{ route($detalleRoute, $s) }}" class="text-decoration-none">{{ $s->id }}</a>
                            </td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($s->labelServiciosContratados(), 80) }}</td>
                            <td>{{ $ciudad !== '' ? $ciudad : '—' }}</td>
                            <td>{{ $s->estado }}</td>
                            <td>{{ $s->nombres }} {{ $s->apellidos }}</td>
                            <td class="text-nowrap">{{ $s->tipo_identificacion }} {{ $s->numero_documento }}</td>
                            <td class="text-center text-nowrap sol-cli-acciones-td">
                                @include('panel.partials._cliente-acciones-solicitud-inner', ['s' => $s, 'detalleRoute' => $detalleRoute])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay solicitudes con los filtros actuales.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if ($solicitudes->total() > 0)
                <div class="p-2 px-3 border-top bg-light d-flex flex-wrap align-items-center justify-content-between gap-2 small">
                    <div class="text-muted">Mostrando {{ $solicitudes->firstItem() ?? 0 }} a {{ $solicitudes->lastItem() ?? 0 }} de {{ $solicitudes->total() }} registros</div>
                    <div>{{ $solicitudes->withQueryString()->links() }}</div>
                </div>
            @endif
        </div>
    </div>

    <p class="text-center text-muted small mb-0">SJ Seguridad Privada LTDA &copy; {{ date('Y') }}. Todos los derechos reservados.</p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
    const chartEstados = @json($chartEstados);
    const chartServicios = @json($chartServicios);
    const chartCiudades = @json($chartCiudades);
    const chartEvolucion = @json($chartEvolucion);
    (function () {
        if (!window.Chart) return;
        const common = { maintainAspectRatio: false, responsive: true };
        const c1 = document.getElementById('chartEstados');
        if (c1) {
            new Chart(c1, {
                type: 'doughnut',
                data: {
                    labels: chartEstados.labels,
                    datasets: [{
                        data: chartEstados.data,
                        backgroundColor: chartEstados.colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    ...common,
                    cutout: '55%',
                    plugins: { legend: { position: 'top' } }
                }
            });
        }
        const c2 = document.getElementById('chartServicios');
        if (c2) {
            new Chart(c2, {
                type: 'bar',
                data: {
                    labels: chartServicios.labels,
                    datasets: [
                        {
                            label: 'Paquetes de servicios',
                            data: chartServicios.paquetes,
                            backgroundColor: 'rgba(107, 114, 128, 0.85)',
                            borderColor: 'rgba(75, 85, 99, 0.9)',
                            borderWidth: 1
                        },
                        {
                            label: 'Servicios individuales',
                            data: chartServicios.individuales,
                            backgroundColor: 'rgba(13, 110, 253, 0.9)',
                            borderColor: 'rgba(10, 88, 202, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    ...common,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        x: { ticks: { maxRotation: 50, minRotation: 25, autoSkip: true, maxTicksLimit: 20 } },
                        y: { beginAtZero: true }
                    }
                }
            });
        }
        const c3 = document.getElementById('chartCiudades');
        if (c3) {
            new Chart(c3, {
                type: 'doughnut',
                data: {
                    labels: chartCiudades.labels,
                    datasets: [{
                        data: chartCiudades.data,
                        backgroundColor: chartCiudades.colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    ...common,
                    cutout: '50%',
                    plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } } }
                }
            });
        }
        const c4 = document.getElementById('chartEvolucion');
        if (c4) {
            new Chart(c4, {
                type: 'line',
                data: {
                    labels: chartEvolucion.labels,
                    datasets: [
                        {
                            label: 'Paquetes de servicios',
                            data: chartEvolucion.paquetes,
                            fill: true,
                            backgroundColor: 'rgba(107, 114, 128, 0.15)',
                            borderColor: 'rgba(107, 114, 128, 0.95)',
                            pointRadius: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Servicios individuales',
                            data: chartEvolucion.individuales,
                            fill: true,
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            borderColor: 'rgba(13, 110, 253, 0.95)',
                            pointRadius: 2,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    ...common,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    })();
</script>
@endpush
