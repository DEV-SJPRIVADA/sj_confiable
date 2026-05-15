@php
    $pid = $idPrefix ?? 'panel-solicitud';
@endphp
<div id="{{ $pid }}-historial" class="card border-0 shadow-sm mb-3 sj-hist-chat-wrap">
    <div class="card-header bg-white">Historial de respuestas</div>
    <div class="card-body card-body--chat p-3">
        @include('panel.solicitudes._fragment-historial-chat', [
            'solicitud' => $solicitud,
            'idPrefix' => $pid,
            'modo' => $modo ?? 'consultor',
        ])
    </div>
</div>

