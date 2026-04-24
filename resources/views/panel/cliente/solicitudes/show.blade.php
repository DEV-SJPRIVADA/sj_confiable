@extends('layouts.app')

@section('title', 'Solicitud #'.$solicitud->id)

@section('content')
    <p class="mb-3">
        <a href="{{ route('panel.cliente.solicitudes.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Volver al listado</a>
    </p>
    @include('panel.solicitudes._detalle', ['solicitud' => $solicitud])
@endsection
