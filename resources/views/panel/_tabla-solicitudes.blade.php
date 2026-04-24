@php
    $nombreServicios = function ($s) {
        if ($s->serviciosPivote->isNotEmpty()) {
            return $s->serviciosPivote->pluck('nombre')->implode(', ');
        }
        return $s->servicio?->nombre ?? '—';
    };
@endphp
<div class="table-responsive rounded-legacy bg-white">
    <table class="table table-legacy table-sm table-bordered table-hover align-middle mb-0" id="tablaPanelSolicitudes">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Evaluado</th>
            <th>Documento</th>
            <th>Archivos</th>
            <th>Evaluados</th>
            <th>Usuario</th>
            <th>Cliente</th>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Creación</th>
            <th class="text-nowrap">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($solicitudes as $s)
            <tr>
                <td>
                    @isset($detalleRoute)
                        <a href="{{ route($detalleRoute, $s) }}" class="text-decoration-none" style="color: #0d6efd;">{{ $s->id }}</a>
                    @else
                        {{ $s->id }}
                    @endisset
                </td>
                <td>{{ $s->nombres }} {{ $s->apellidos }}</td>
                <td>{{ $s->tipo_identificacion }} {{ $s->numero_documento }}</td>
                <td>{{ $s->documentos_count }}</td>
                <td>{{ $s->evaluados_count }}</td>
                <td>{{ $s->creador?->usuario ?? '—' }}</td>
                <td>{{ $s->creador?->cliente?->razon_social ?? '—' }}</td>
                <td>{{ $nombreServicios($s) }}</td>
                <td>{{ $s->estado }}</td>
                <td>{{ $s->fecha_creacion?->format('Y-m-d H:i') ?? '—' }}</td>
                <td class="text-nowrap">
                    @isset($detalleRoute)
                        <a class="btn btn-sm btn-primary" href="{{ route($detalleRoute, $s) }}"><i class="fas fa-eye me-1 d-none d-md-inline" aria-hidden="true"></i>Ver detalle</a>
                    @else
                        <span class="text-muted">—</span>
                    @endisset
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center text-muted">No hay solicitudes para mostrar.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
