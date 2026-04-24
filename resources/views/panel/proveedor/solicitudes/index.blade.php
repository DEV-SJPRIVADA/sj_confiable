@extends('layouts.app')

@section('title', 'Solicitudes asignadas — Asociado')

@section('content')
    <div class="header-container-solicitudes">
        <h1>Solicitudes</h1>
    </div>
    <p class="text-muted small mb-3">Solicitudes asignadas a su asociado. La mediación con el cliente la gestiona SJ Seguridad.</p>
    @include('panel._tabla-solicitudes', ['solicitudes' => $solicitudes, 'detalleRoute' => $detalleRoute])
@endsection
