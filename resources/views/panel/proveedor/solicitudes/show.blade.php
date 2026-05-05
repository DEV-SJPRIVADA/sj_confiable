@extends('layouts.app')

@section('title', 'Solicitud #'.$solicitud->id)

@push('styles')
<style>
    .panel-proveedor-solicitudes-page {
        background: #f1f3f5;
        margin-left: -0.75rem;
        margin-right: -0.75rem;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="panel-proveedor-solicitudes-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pt-2">
        <p class="mb-0">
            <a href="{{ route('panel.proveedor.solicitudes.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Volver al listado</a>
        </p>
        @can('respondAsProveedor', $solicitud)
            <a href="{{ route('panel.proveedor.solicitudes.respuesta', $solicitud) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-reply fa-flip-horizontal me-1" aria-hidden="true"></i>Gestionar respuesta
            </a>
        @endcan
    </div>
    @include('panel.solicitudes._detalle', ['solicitud' => $solicitud])
</div>
@endsection
