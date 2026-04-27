@extends('layouts.app')

@section('title', 'Inicio — Consultor')

@push('styles')
<style>
    .panel-consultor-home { background: #f1f3f5; }
    .panel-consultor-home .dashboard-filters { background: #e9ecef; border-radius: 0.35rem; }
    .panel-consultor-home .widget-title { color: #0d6efd; font-weight: 600; font-size: 0.95rem; }
    .panel-consultor-home .widget-title i { opacity: 0.85; }
    .panel-consultor-home .stat-tile { border: 1px solid #e0e0e0; border-radius: 0.35rem; }
    .panel-consultor-home .table thead { background: linear-gradient(180deg, #e7f1ff 0%, #f8f9fa 100%); }
    .panel-consultor-home .chart-box { min-height: 280px; position: relative; }
</style>
@endpush

@section('content')
@php
    $f = $filtros ?? [];
@endphp
<div class="panel-consultor-home">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="fw-light mb-1" style="font-size:1.75rem;">Inicio</h1>
            <p class="text-muted small mb-0">Resumen de solicitudes y accesos (vista alineada al panel legado).</p>
        </div>
    </div>

    <form method="get" action="{{ route('panel.consultor.inicio') }}" class="dashboard-filters p-3 mb-3">
        <div class="row g-2 small align-items-end">
            <div class="col-12 col-md-6 col-xl-2">
                <label class="form-label mb-0 text-muted">Empresa cliente</label>
                <select name="id_cliente" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id_cliente }}" @selected((string)($f['id_cliente'] ?? '') === (string)$c->id_cliente)>{{ $c->razon_social }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-2">
                <label class="form-label mb-0 text-muted">Servicio</label>
                <select name="servicio_id" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach ($servicios as $svc)
                        <option value="{{ $svc->id_servicio }}" @selected((string)($f['servicio_id'] ?? '') === (string)$svc->id_servicio)>{{ $svc->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-2">
                <label class="form-label mb-0 text-muted">Estado solicitud</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach ($estados as $e)
                        <option value="{{ $e }}" @selected(($f['estado'] ?? '') === $e)>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-6 col-xl-2">
                <label class="form-label mb-0 text-muted">Fecha inicio</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="{{ $f['desde'] ?? '' }}">
            </div>
            <div class="col-6 col-md-6 col-xl-2">
                <label class="form-label mb-0 text-muted">Fecha fin</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="{{ $f['hasta'] ?? '' }}">
            </div>
            <div class="col-12 col-xl-2 d-flex flex-wrap gap-1">
                <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
                <a href="{{ route('panel.consultor.inicio') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-tile bg-white p-2 p-md-3 text-center h-100">
                <div class="fs-4 fw-bold text-primary">{{ $countUsuarios }}</div>
                <div class="small text-muted">Usuarios activos</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-tile bg-white p-2 p-md-3 text-center h-100">
                <div class="fs-4 fw-bold text-primary">{{ $countClientes }}</div>
                <div class="small text-muted">Clientes</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-tile bg-white p-2 p-md-3 text-center h-100">
                <div class="fs-4 fw-bold text-primary">{{ $countAsociados }}</div>
                <div class="small text-muted">Asociados</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-tile bg-white p-2 p-md-3 text-center h-100">
                <div class="fs-4 fw-bold text-primary">{{ $countSolicitudesActivas }}</div>
                <div class="small text-muted">Solicitudes activas</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-tile bg-white p-2 p-md-3 text-center h-100">
                <div class="fs-4 fw-bold text-warning">{{ $countSolicitudesUsuarioPendientes }}</div>
                <div class="small text-muted">Solic. usuarios (pend.)</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="widget-title mb-2"><i class="fas fa-chart-pie me-2"></i>Solicitudes por estado</h2>
                    <div class="chart-box" style="max-height: 300px;">
                        <canvas id="chartEstados" aria-label="Solicitudes por estado"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="widget-title mb-2"><i class="fas fa-chart-bar me-2"></i>Solicitudes por servicio</h2>
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
                    <h2 class="widget-title mb-2"><i class="fas fa-building me-2"></i>Solicitudes por empresa</h2>
                    <div class="chart-box" style="max-height: 300px;">
                        <canvas id="chartEmpresas" aria-label="Solicitudes por empresa"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="widget-title mb-2"><i class="fas fa-chart-line me-2"></i>Evolución de solicitudes</h2>
                    <div class="chart-box" style="max-height: 300px;">
                        <canvas id="chartEvolucion" aria-label="Evolución de solicitudes"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-0">
                    <h2 class="widget-title p-3 pb-0 mb-2"><i class="fas fa-th me-2"></i>Solicitudes recientes</h2>
                    <div class="table-responsive">
                        <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Empresa</th>
                                    <th>Servicio</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSolicitudes as $s)
                                    <tr>
                                        <td>{{ $s->id }}</td>
                                        <td>
                                            @php
                                                $emp = trim((string) $s->empresa_solicitante);
                                            @endphp
                                            {{ $emp !== '' ? \Illuminate\Support\Str::limit($emp, 36) : ($s->creador?->cliente?->razon_social ?? '—') }}
                                        </td>
                                        <td>{{ $s->servicio?->nombre ?? $s->paquete?->nombre ?? '—' }}</td>
                                        <td>{{ $s->estado }}</td>
                                        <td class="text-nowrap">{{ $s->fecha_creacion?->format('Y-m-d H:i') ?? '—' }}</td>
                                        <td>{{ $s->creador?->usuario ?? '—' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('panel.consultor.solicitudes.show', $s) }}" class="btn btn-sm btn-outline-primary" title="Detalle"><i class="fas fa-chevron-right"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Sin registros con los filtros actuales.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-0">
                    <h2 class="widget-title p-3 pb-0 mb-2"><i class="fas fa-th me-2"></i>Peticiones usuarios</h2>
                    <div class="table-responsive">
                        <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Solicitante</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSolicitudesUsuario as $su)
                                    <tr>
                                        <td>{{ $su->id_solicitud }}</td>
                                        <td>{{ $su->cliente?->razon_social ?? '—' }}</td>
                                        <td>{{ $su->solicitante?->usuario ?? '—' }}</td>
                                        <td>{{ $su->tipo }}</td>
                                        <td>{{ $su->estado }}</td>
                                        <td class="text-nowrap">{{ $su->fecha_solicitud?->format('Y-m-d H:i') ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Sin registros recientes</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 g-md-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('panel.consultor.usuarios.index') }}" class="d-block p-2 p-md-3 text-center text-decoration-none stat-tile bg-white h-100 small text-body">
                <i class="fas fa-users d-block text-primary fs-4 mb-1"></i>Usuarios
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('panel.consultor.clientes.index') }}" class="d-block p-2 p-md-3 text-center text-decoration-none stat-tile bg-white h-100 small text-body">
                <i class="fas fa-building d-block text-primary fs-4 mb-1"></i>Clientes
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('panel.consultor.asociados.index') }}" class="d-block p-2 p-md-3 text-center text-decoration-none stat-tile bg-white h-100 small text-body">
                <i class="fas fa-handshake d-block text-primary fs-4 mb-1"></i>Asociados
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('panel.consultor.solicitudes.index') }}" class="d-block p-2 p-md-3 text-center text-decoration-none stat-tile bg-white h-100 small text-body">
                <i class="fas fa-clipboard-list d-block text-primary fs-4 mb-1"></i>Confiabilidad
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('panel.consultor.solicitudes-usuarios.index') }}" class="d-block p-2 p-md-3 text-center text-decoration-none stat-tile bg-white h-100 small text-body">
                <i class="fas fa-user-cog d-block text-primary fs-4 mb-1"></i>Usuarios (sol.)
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('panel.consultor.informes.index') }}" class="d-block p-2 p-md-3 text-center text-decoration-none stat-tile bg-white h-100 small text-body">
                <i class="fas fa-chart-bar d-block text-primary fs-4 mb-1"></i>Informes
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
    const chartEstados = @json($chartEstados);
    const chartServicios = @json($chartServicios);
    const chartEmpresas = @json($chartEmpresas);
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
                            label: 'Paquetes de Servicios',
                            data: chartServicios.paquetes,
                            backgroundColor: 'rgba(107, 114, 128, 0.85)',
                            borderColor: 'rgba(75, 85, 99, 0.9)',
                            borderWidth: 1
                        },
                        {
                            label: 'Servicios Individuales',
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
        const c3 = document.getElementById('chartEmpresas');
        if (c3) {
            new Chart(c3, {
                type: 'doughnut',
                data: {
                    labels: chartEmpresas.labels,
                    datasets: [{
                        data: chartEmpresas.data,
                        backgroundColor: chartEmpresas.colors,
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
                            label: 'Paquetes de Servicios',
                            data: chartEvolucion.paquetes,
                            fill: true,
                            backgroundColor: 'rgba(107, 114, 128, 0.15)',
                            borderColor: 'rgba(107, 114, 128, 0.95)',
                            pointRadius: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Servicios Individuales',
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
