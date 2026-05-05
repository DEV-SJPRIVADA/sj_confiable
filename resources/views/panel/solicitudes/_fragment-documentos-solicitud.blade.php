@php
    $idPrefix = $idPrefix ?? 'panel-solicitud';
    $clienteDl = ($clienteDocumentDownloads ?? false) === true;
    $urlDocumentoPublico = static function (\App\Models\Documento $d): string {
        $r = ltrim((string) ($d->ruta_documento ?? ''), '/');
        if ($r === '') {
            return '#';
        }
        if (str_starts_with($r, 'uploads/')) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($r);
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url('uploads/'.$r);
    };
@endphp
@if ($clienteDl && $solicitud->documentos->isNotEmpty())
    <div id="{{ $idPrefix }}-documentos" data-section="{{ $idPrefix }}-documentos" class="card border-0 mb-0 solicitud-anchor-documentos cli-solic-estado-card" style="border:1px solid #dee2e6 !important;">
        <div class="card-header bg-white border-bottom">Documentos</div>
        <ul class="list-group list-group-flush small">
            @foreach ($solicitud->documentos as $d)
                <li class="list-group-item d-flex justify-content-between align-items-start gap-2">
                    <div class="min-w-0 d-flex gap-2">
                        <i class="fas fa-file-pdf text-danger mt-1 flex-shrink-0" aria-hidden="true"></i>
                        <div class="min-w-0">
                            <span class="text-break d-block fw-semibold">{{ $d->nombre_documento }}</span>
                            <span class="text-danger" style="font-size:0.75rem;">Documento Solicitud</span>
                        </div>
                    </div>
                    <a href="{{ route('panel.cliente.solicitudes.archivos.documento', ['solicitud' => $solicitud->id, 'documento' => $d->id]) }}"
                       class="btn btn-primary btn-sm rounded-circle flex-shrink-0 d-inline-flex align-items-center justify-content-center text-white"
                       style="width:2.35rem;height:2.35rem;padding:0;line-height:1;"
                       title="Descargar"
                       aria-label="Descargar {{ $d->nombre_documento }}">
                        <i class="fas fa-download" aria-hidden="true"></i>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@elseif ($solicitud->documentos->isNotEmpty())
    <div id="{{ $idPrefix }}-documentos" data-section="{{ $idPrefix }}-documentos" class="card border-0 shadow-sm mb-0 solicitud-anchor-documentos">
        <div class="card-header bg-white">Documentos</div>
        <ul class="list-group list-group-flush small">
            @foreach ($solicitud->documentos as $d)
                @php
                    $href = $urlDocumentoPublico($d);
                @endphp
                <li class="list-group-item d-flex justify-content-between align-items-start gap-2">
                    <div class="min-w-0 d-flex gap-2">
                        <i class="fas fa-file-pdf text-danger mt-1 flex-shrink-0" aria-hidden="true"></i>
                        <div class="min-w-0">
                            @if ($href !== '#')
                                <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="text-break d-block fw-semibold text-decoration-none">{{ $d->nombre_documento }}</a>
                            @else
                                <span class="text-break d-block fw-semibold">{{ $d->nombre_documento }}</span>
                            @endif
                            <span class="text-muted" style="font-size:0.75rem;">Documento solicitud</span>
                        </div>
                    </div>
                    @if ($href !== '#')
                        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm rounded-circle flex-shrink-0 d-inline-flex align-items-center justify-content-center text-white"
                           style="width:2.35rem;height:2.35rem;padding:0;line-height:1;"
                           title="Abrir PDF"
                           aria-label="Abrir {{ $d->nombre_documento }}">
                            <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div id="{{ $idPrefix }}-documentos" data-section="{{ $idPrefix }}-documentos" class="card border-0 shadow-sm mb-0 solicitud-anchor-documentos">
        <div class="card-header bg-white">Documentos</div>
        <p class="p-3 mb-0 text-muted small">Sin documentos adjuntos.</p>
    </div>
@endif
