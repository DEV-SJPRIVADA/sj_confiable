@php
    $pid = $idPrefix ?? 'panel-solicitud';
@endphp
<div id="{{ $pid }}-historial" class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">Historial de respuestas (auditoría)</div>
    <div class="card-body p-0">
        @if($solicitud->historialRespuestas->isEmpty())
            <p class="p-3 mb-0 text-muted">Sin movimientos registrados en historial.</p>
        @else
            <div class="table-responsive">
                <table class="table table-legacy table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Asoc.</th>
                        <th>Texto / estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($solicitud->historialRespuestas as $h)
                        <tr>
                            <td class="text-nowrap">{{ $h->fecha_respuesta?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ $h->usuario?->usuario ?? '—' }}</td>
                            <td>{{ $h->usuario?->proveedor?->nombre_comercial ?? '—' }}</td>
                            <td><small>{{ \Illuminate\Support\Str::limit($h->respuesta, 200) }} @if($h->estado_anterior) <span class="text-muted">({{ $h->estado_anterior }} → {{ $h->estado_actual }})</span> @endif</small></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
