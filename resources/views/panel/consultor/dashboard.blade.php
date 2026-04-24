@extends('layouts.app')

@section('title', 'Inicio — Consultor')

@section('content')
    <div class="mb-4">
        <h1 class="fw-light" style="font-size:1.75rem;">Panel principal</h1>
        <p class="text-muted small">Resumen y accesos a los módulos (alineado al flujo del sistema legado).</p>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <div class="fs-3 fw-bold text-primary">{{ $countUsuarios }}</div>
                <div class="small text-muted">Usuarios activos</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <div class="fs-3 fw-bold text-primary">{{ $countClientes }}</div>
                <div class="small text-muted">Clientes</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <div class="fs-3 fw-bold text-primary">{{ $countAsociados }}</div>
                <div class="small text-muted">Asociados</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <div class="fs-3 fw-bold text-primary">{{ $countSolicitudesActivas }}</div>
                <div class="small text-muted">Solicitudes (activas)</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <div class="fs-3 fw-bold text-warning">{{ $countSolicitudesUsuarioPendientes }}</div>
                <div class="small text-muted">Solic. usuarios (pend.)</div>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('panel.consultor.usuarios.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 p-3 d-block text-body">
                <h2 class="h6 text-primary"><i class="fas fa-users me-2"></i>Usuarios</h2>
                <p class="small text-muted mb-0">Listado y gestión (listado de solo lectura en esta fase).</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('panel.consultor.clientes.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 p-3 d-block text-body">
                <h2 class="h6 text-primary"><i class="fas fa-building me-2"></i>Clientes</h2>
                <p class="small text-muted mb-0">Organizaciones cliente registradas en la plataforma.</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('panel.consultor.asociados.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 p-3 d-block text-body">
                <h2 class="h6 text-primary"><i class="fas fa-handshake me-2"></i>Asociados de negocios</h2>
                <p class="small text-muted mb-0">Proveedores de servicios (asociados).</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('panel.consultor.solicitudes.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 p-3 d-block text-body">
                <h2 class="h6 text-primary"><i class="fas fa-clipboard-list me-2"></i>Confiabilidad</h2>
                <p class="small text-muted mb-0">Solicitudes de confiabilidad (evaluados).</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('panel.consultor.solicitudes-usuarios.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 p-3 d-block text-body">
                <h2 class="h6 text-primary"><i class="fas fa-user-cog me-2"></i>Solicitudes de usuarios</h2>
                <p class="small text-muted mb-0">Altas, bajas o cambios de usuarios solicitados por clientes.</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('panel.consultor.informes.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 p-3 d-block text-body">
                <h2 class="h6 text-primary"><i class="fas fa-chart-bar me-2"></i>Informes</h2>
                <p class="small text-muted mb-0">Filtrado y listado de solicitudes por criterios.</p>
            </a>
        </div>
    </div>
@endsection
