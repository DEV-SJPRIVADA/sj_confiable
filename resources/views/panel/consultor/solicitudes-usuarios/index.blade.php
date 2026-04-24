@extends('layouts.app')

@section('title', 'Solicitudes de usuarios — Consultor')

@section('content')
    <div class="mb-3">
        <h1 class="fw-light" style="font-size:1.75rem;">Solicitudes de gestión de usuarios</h1>
        <p class="text-muted small mb-0">Peticiones de clientes. Solo las pendientes pueden aprobarse o rechazarse; el comentario es obligatorio.</p>
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
                <th class="text-nowrap" style="min-width: 14rem;">Acciones</th>
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
                    <td>
                        @if ($s->estado === 'Pendiente')
                            @can('respond', $s)
                            <form method="post" action="{{ route('panel.consultor.solicitudes-usuarios.responder', $s) }}" class="d-flex flex-column gap-1 small">
                                @csrf
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <label class="visually-hidden" for="estado_{{ $s->id_solicitud }}">Estado</label>
                                    <select name="estado" id="estado_{{ $s->id_solicitud }}" class="form-select form-select-sm" style="max-width: 10rem;" required>
                                        <option value="" disabled selected>—</option>
                                        <option value="Aprobada">Aprobar</option>
                                        <option value="Rechazada">Rechazar</option>
                                    </select>
                                </div>
                                <label class="form-label small mb-0" for="com_{{ $s->id_solicitud }}">Comentario</label>
                                <textarea name="comentario" id="com_{{ $s->id_solicitud }}" class="form-control form-control-sm" rows="2" required maxlength="2000" placeholder="Motivo o detalle de la respuesta"></textarea>
                                <button type="submit" class="btn btn-sm btn-primary align-self-start">Enviar respuesta</button>
                            </form>
                            @else
                                <span class="text-muted">Sin permiso</span>
                            @endcan
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">No hay solicitudes de este tipo.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $solicitudes->links() }}
    </div>
@endsection
