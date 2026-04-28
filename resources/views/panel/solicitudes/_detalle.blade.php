@php
    $idPrefix = $idPrefix ?? 'panel-solicitud';
@endphp
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-bold text-primary">Solicitud #{{ $solicitud->id }} — <span class="text-body">{{ $solicitud->estado }}</span></div>
    <div class="card-body">
        <div class="row small">
            <div class="col-md-4 mb-2"><span class="text-muted">Evaluado</span><br>{{ $solicitud->nombres }} {{ $solicitud->apellidos }}</div>
            <div class="col-md-4 mb-2"><span class="text-muted">Documento</span><br>{{ $solicitud->tipo_identificacion }} {{ $solicitud->numero_documento }}</div>
            <div class="col-md-4 mb-2"><span class="text-muted">Servicio(s)</span><br>{{ $solicitud->labelServiciosContratados() }}</div>
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
@include('panel.solicitudes._fragment-documentos-solicitud', ['solicitud' => $solicitud, 'idPrefix' => $idPrefix])
@include('panel.solicitudes._fragment-historial-respuestas', ['solicitud' => $solicitud, 'idPrefix' => $idPrefix])
