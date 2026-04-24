@extends('layouts.app')

@section('title', 'Solicitudes — Consultor')

@section('content')
    <div class="header-container-solicitudes">
        <h1>Solicitudes</h1>
    </div>
    <p class="text-muted small mb-3">Listado global. Para gestionar o asignar, abre el detalle con el botón <strong>Ver detalle</strong> o haciendo clic en el <strong>ID</strong> de la fila.</p>
    @include('panel._tabla-solicitudes', [
        'solicitudes' => $solicitudes,
        'detalleRoute' => $detalleRoute,
    ])
@endsection
