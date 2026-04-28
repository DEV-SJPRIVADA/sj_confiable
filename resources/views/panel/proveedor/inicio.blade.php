@extends('layouts.app')

@section('title', 'Panel del proveedor')

@push('styles')
<style>
    .panel-proveedor-home {
        width: 100%;
        max-width: none;
        box-sizing: border-box;
        background: #f1f3f5;
        /* Compensa el padding horizontal del `main.container-fluid` para usar todo el ancho como el legado */
        margin-left: -0.75rem;
        margin-right: -0.75rem;
        padding-left: clamp(1rem, 2.5vw, 2.5rem);
        padding-right: clamp(1rem, 2.5vw, 2.5rem);
        padding-bottom: 2rem;
    }
    .panel-proveedor-home .pv-bienvenida h1 {
        font-size: clamp(1.25rem, 2.5vw, 1.85rem);
        font-weight: 700;
        color: #212529;
    }
    .panel-proveedor-home .pv-bienvenida .pv-sub {
        color: #6c757d;
        font-size: 0.95rem;
    }
    .pv-card-proveedor {
        border-radius: 0.5rem;
        border: 1px solid #e2e6ea;
        box-shadow: 0 0.2rem 0.65rem rgba(0, 0, 0, 0.06);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        overflow: hidden;
        height: 100%;
        background: #fff;
    }
    .pv-card-proveedor:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.35rem 1rem rgba(0, 0, 0, 0.1);
    }
    .pv-card-proveedor .pv-card-icon-wrap {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.85rem;
    }
    .pv-card-proveedor h2 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.35rem;
    }
    .pv-card-proveedor p.text-muted {
        font-size: 0.875rem;
        min-height: 2.65rem;
    }
    .pv-consejos {
        background: #e9ecef;
        border: 1px solid #dee2e6;
        border-radius: 0.45rem;
    }
    .pv-consejos h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #343a40;
    }
    .pv-consejos ul { padding-left: 1.35rem; margin-bottom: 0; color: #495057; font-size: 0.92rem; }
</style>
@endpush

@section('content')
<div class="panel-proveedor-home">
    <div class="pv-bienvenida text-center mb-4 pt-2">
        <h1 class="mb-1">Bienvenido, {{ $bienvenidaNombre }}</h1>
        <p class="pv-sub mb-0">Panel del proveedor</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="pv-card-proveedor p-4">
                <div class="pv-card-icon-wrap bg-primary bg-opacity-10 text-primary ms-0">
                    <i class="fas fa-check-square fa-2x" aria-hidden="true"></i>
                </div>
                <h2>Solicitudes</h2>
                <p class="text-muted small mb-3">Consulta y gestiona tus solicitudes asignadas.</p>
                <a href="{{ route('panel.proveedor.solicitudes.index') }}" class="btn btn-primary w-100 text-uppercase fw-semibold py-2">Ir a Solicitudes</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pv-card-proveedor p-4">
                <div class="pv-card-icon-wrap bg-success bg-opacity-10 text-success ms-0">
                    <i class="fas fa-user fa-2x" aria-hidden="true"></i>
                </div>
                <h2>Mi Perfil</h2>
                <p class="text-muted small mb-3">Actualiza tu información y credenciales.</p>
                <a href="{{ route('panel.proveedor.perfil.show') }}" class="btn btn-success w-100 text-uppercase fw-semibold py-2">Ir a Perfil</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pv-card-proveedor p-4">
                <div class="pv-card-icon-wrap bg-warning bg-opacity-50 text-warning-emphasis ms-0">
                    <i class="fas fa-bell fa-2x" aria-hidden="true"></i>
                </div>
                <h2>Notificaciones</h2>
                <p class="text-muted small mb-3">Mantente al día con las novedades.</p>
                <button type="button" class="btn btn-warning text-dark w-100 text-uppercase fw-semibold py-2" data-bs-toggle="modal" data-bs-target="#modalNotificacionesProveedor">Ver notificaciones</button>
            </div>
        </div>
    </div>

    <div class="pv-consejos p-3 p-md-4">
        <h3 class="mb-3 d-flex align-items-center gap-2">
            <i class="fas fa-info-circle text-primary" aria-hidden="true"></i>
            Consejos rápidos
        </h3>
        <ul class="mb-0">
            <li>Usa el menú &laquo;Solicitudes&raquo; para ver el detalle y responder.</li>
            <li>Puedes actualizar tus datos desde &laquo;Mi Perfil&raquo;.</li>
            <li>Las notificaciones te avisarán sobre cambios importantes.</li>
        </ul>
    </div>
</div>
@endsection
