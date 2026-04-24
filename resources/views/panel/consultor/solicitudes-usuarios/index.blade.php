@extends('layouts.app')

@section('title', 'Solicitudes de usuarios — Consultor')

@section('content')
    <div class="mb-3">
        <h1 class="fw-light" style="font-size:1.75rem;">Solicitudes de gestión de usuarios</h1>
        <p class="text-muted small mb-0">Peticiones de clientes (crear, modificar, inactivar usuarios en su organización). Responder (legado) se añadirá con el módulo completo.</p>
    </div>
    <div class="table-responsive rounded-legacy bg-white">
        <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Cliente</th>
                <th>Solicitante</th>
                <th>Responde</th>
                <th>Fecha solicitud</th>
                <th>Datos (JSON)</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($solicitudes as $s)
                <tr>
                    <td>{{ $s->id_solicitud }}</td>
                    <td>{{ $s->tipo }}</td>
                    <td>{{ $s->estado }}</td>
                    <td>{{ $s->cliente?->razon_social ?? '—' }}</td>
                    <td>{{ $s->solicitante?->usuario ?? '—' }}</td>
                    <td>{{ $s->usuarioResponde?->usuario ?? '—' }}</td>
                    <td class="text-nowrap">{{ $s->fecha_solicitud?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="small"><code class="text-break" style="font-size:0.7rem;">{{ \Illuminate\Support\Str::limit($s->datos_usuario, 80) }}</code></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No hay solicitudes de este tipo.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $solicitudes->links() }}
    </div>
@endsection
