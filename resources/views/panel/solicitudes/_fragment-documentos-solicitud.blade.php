@php
    $pid = $idPrefix ?? 'panel-solicitud';
@endphp
@if($solicitud->documentos->isNotEmpty())
    <div id="{{ $pid }}-documentos" class="card border-0 shadow-sm mb-0">
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
@else
    <div id="{{ $pid }}-documentos" class="card border-0 shadow-sm mb-0">
        <div class="card-header bg-white">Documentos</div>
        <p class="p-3 mb-0 text-muted small">Sin documentos adjuntos.</p>
    </div>
@endif
