@extends('layouts.app')

@section('title', 'Mis solicitudes — Cliente')

@section('content')
    <div class="header-container-solicitudes">
        <h1>Solicitudes</h1>
    </div>
    <p class="text-muted small mb-3">Solo solicitudes de usuarios de su misma organización (mismo criterio que el sistema legado).</p>
    @include('panel._tabla-solicitudes', ['solicitudes' => $solicitudes, 'detalleRoute' => $detalleRoute])
@endsection
