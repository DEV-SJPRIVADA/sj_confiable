@php
    $nombreServicios = function ($s) {
        if ($s->serviciosPivote->isNotEmpty()) {
            return $s->serviciosPivote->pluck('nombre')->implode(', ');
        }
        return $s->servicio?->nombre ?? '—';
    };
@endphp
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-bold text-primary">Solicitud #{{ $solicitud->id }} — <span class="text-body">{{ $solicitud->estado }}</span></div>
    <div class="card-body">
        <div class="row small">
            <div class="col-md-4 mb-2"><span class="text-muted">Evaluado</span><br>{{ $solicitud->nombres }} {{ $solicitud->apellidos }}</div>
            <div class="col-md-4 mb-2"><span class="text-muted">Documento</span><br>{{ $solicitud->tipo_identificacion }} {{ $solicitud->numero_documento }}</div>
            <div class="col-md-4 mb-2"><span class="text-muted">Servicio(s)</span><br>{{ $nombreServicios($solicitud) }}</div>
            <div class="col-md-4 mb-2"><span class="text-muted">Usuario creador</span><br>{{ $solicitud->creador?->usuario ?? '—' }}</div>
            <div class="col-md-4 mb-2"><span class="text-muted">Cliente (razón social)</span><br>{{ $solicitud->creador?->cliente?->razon_social ?? '—' }}</div>
            <div class="col-md-4 mb-2"><span class="text-muted">Proveedor asignado</span><br>{{ $solicitud->proveedorAsignado?->nombre_comercial ?? $solicitud->proveedorAsignado?->razon_social_proveedor ?? '—' }}</div>
        </div>
    </div>
</div>
@if($solicitud->evaluados->isNotEmpty())
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">Evaluados ({{ $solicitud->evaluados->count() }})</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-legacy table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Nombres</th>
                        <th>Documento</th>
                        <th>Correo</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($solicitud->evaluados as $e)
                        <tr>
                            <td>{{ $e->nombres }} {{ $e->apellidos }}</td>
                            <td>{{ $e->tipo_identificacion }} {{ $e->numero_documento }}</td>
                            <td>—</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@if($solicitud->documentos->isNotEmpty())
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">Documentos</div>
        <ul class="list-group list-group-flush small">
            @foreach($solicitud->documentos as $d)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $d->nombre_documento }}</span>
                    <code class="text-break">{{ $d->ruta_documento }}</code>
                </li>
            @endforeach
        </ul>
    </div>
@endif
<div class="card border-0 shadow-sm mb-3">
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
