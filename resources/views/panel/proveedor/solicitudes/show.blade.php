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
    <p class="mb-3 pt-2">
        <a href="{{ route('panel.proveedor.solicitudes.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Volver al listado</a>
    </p>
    @include('panel.solicitudes._detalle', ['solicitud' => $solicitud])
</div>
@endsection
