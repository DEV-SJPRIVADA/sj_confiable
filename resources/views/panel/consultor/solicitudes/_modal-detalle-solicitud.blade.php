@php
    $servicioLinea = trim($s->labelServiciosContratados());
    if ($servicioLinea === '' || $servicioLinea === '—') {
        $servicioLinea = '—';
    }
    $paqDescExtra = '';
    $paq = $s->paquete;
    if ($paq !== null) {
        $d = trim((string) ($paq->descripcion ?? ''));
        $n = trim((string) ($paq->nombre ?? ''));
        if ($d !== '' && $d !== $n) {
            $paqDescExtra = $d;
        }
    }
    $fmt = function ($v): string {
        if (is_string($v) && trim($v) !== '') {
            return $v;
        }
        if (is_numeric($v) && (string) $v !== '') {
            return (string) $v;
        }

        return '—';
    };
@endphp
<div class="modal fade solicitud-modal-detalle" id="modalDetalleSolicitud{{ $s->id }}" tabindex="-1" aria-labelledby="modalDetalleSolicitudLabel{{ $s->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg solicitud-modal-detalle__content">
            <div class="modal-header solicitud-modal-detalle__bar border-0 text-white">
                <h2 class="modal-title fs-5 mb-0 fw-normal" id="modalDetalleSolicitudLabel{{ $s->id }}">Detalle de Solicitud</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body bg-white text-body pt-3 pb-2">
                <h2 class="h6 text-primary mb-3 fw-bold">Información de la Solicitud</h2>
                <div class="row g-2 small">
                    <div class="col-md-6 pe-md-3">
                        <p class="mb-2"><strong>ID:</strong> {{ $s->id }}</p>
                        <p class="mb-2 text-break"><strong>Servicio contratado:</strong> {{ $servicioLinea }}</p>
                        @if ($paqDescExtra !== '')
                            <p class="mb-2 text-muted small text-break">{{ $paqDescExtra }}</p>
                        @endif
                        <p class="mb-2"><strong>Ciudad de Solicitud:</strong> {{ $fmt($s->ciudad_solicitud_servicio ?? null) }}</p>
                        <p class="mb-2"><strong>Ciudad de Residencia:</strong> {{ $fmt($s->ciudad_residencia_evaluado ?? null) }}</p>
                        <p class="mb-2"><strong>Documento:</strong> {{ $fmt($s->numero_documento ?? null) }}</p>
                        <p class="mb-2"><strong>Teléfono:</strong> {{ $fmt($s->telefono_fijo ?? null) }}</p>
                        <p class="mb-0"><strong>Cargo:</strong> {{ $fmt($s->cargo_candidato ?? null) }}</p>
                    </div>
                    <div class="col-md-6 ps-md-2">
                        <p class="mb-2"><strong>Estado:</strong> {{ $fmt($s->estado ?? null) }}</p>
                        <p class="mb-2 text-break"><strong>Empresa:</strong> {{ $fmt($s->empresa_solicitante ?? null) }}</p>
                        <p class="mb-2"><strong>Nombres:</strong> {{ \Illuminate\Support\Str::upper(trim((string) ($s->nombres ?? ''))) !== '' ? \Illuminate\Support\Str::upper(trim((string) $s->nombres)) : '—' }}</p>
                        <p class="mb-2"><strong>Apellidos:</strong> {{ \Illuminate\Support\Str::upper(trim((string) ($s->apellidos ?? ''))) !== '' ? \Illuminate\Support\Str::upper(trim((string) $s->apellidos)) : '—' }}</p>
                        <p class="mb-2"><strong>Celular:</strong> {{ $fmt($s->celular ?? null) }}</p>
                        <p class="mb-0 text-break"><strong>Comentarios:</strong> {{ $fmt($s->comentarios ?? null) }}</p>
                    </div>
                </div>
                <hr class="text-secondary opacity-25 my-3">
                <h2 class="h6 text-primary mb-2 fw-bold">Archivos adjuntos</h2>
                <div class="solicitud-modal-detalle__archivos rounded-2 p-3 small mb-3">
                    @php
                        $docsCliente = $s->relationLoaded('documentos') ? $s->documentos : $s->documentos()->get();
                        $docsOperativos = collect();
                        $madres = $s->relationLoaded('respuestasMadre')
                            ? $s->respuestasMadre
                            : $s->respuestasMadre()->orderByDesc('fecha_creacion')->with('documentos')->get();
                        foreach ($madres as $rm) {
                            $dSet = $rm->relationLoaded('documentos') ? $rm->documentos : $rm->documentos()->get();
                            foreach ($dSet as $dr) {
                                $docsOperativos->push($dr);
                            }
                        }
                        $hayArchivos = $docsCliente->isNotEmpty() || $docsOperativos->isNotEmpty();
                    @endphp
                    @if (! $hayArchivos)
                        <p class="mb-0">No hay archivos adjuntos.</p>
                    @else
                        <ul class="list-unstyled mb-0">
                            @foreach ($docsCliente as $d)
                                <li class="mb-2">
                                    <span class="text-muted text-uppercase" style="font-size: 0.7rem;">Solicitud</span><br>
                                    <span>· {{ $d->nombre_documento ?? '—' }}</span>
                                </li>
                            @endforeach
                            @foreach ($docsOperativos as $dr)
                                <li class="mb-2">
                                    <span class="text-muted text-uppercase" style="font-size: 0.7rem;">Respuesta asociado</span><br>
                                    <span>· {{ $dr->nombre_documentoResp ?? '—' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @if ($s->id_proveedor && $s->proveedorAsignado)
                    <div class="solicitud-modal-detalle__asoc border rounded-2 p-3 small d-flex align-items-start gap-2">
                        <span class="solicitud-modal-detalle__asoc-badge" aria-hidden="true"><i class="fas fa-user-tie text-dark"></i></span>
                        <p class="mb-0">Asociado de negocio: <strong>{{ $s->proveedorAsignado->nombre_comercial ?? $s->proveedorAsignado->razon_social_proveedor ?? '—' }}</strong></p>
                    </div>
                @else
                    <div class="solicitud-modal-detalle__asoc border rounded-2 p-3 small d-flex align-items-start gap-2">
                        <span class="solicitud-modal-detalle__asoc-badge" aria-hidden="true"><i class="fas fa-user text-dark"></i></span>
                        <p class="mb-0">No hay asociado de negocio asignado a esta solicitud.</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer solicitud-modal-detalle__bar border-0 d-flex justify-content-end">
                <button type="button" class="btn btn-outline-light solicitud-modal-detalle__btn-cerrar text-uppercase fw-semibold" data-bs-dismiss="modal">X Cerrar</button>
            </div>
        </div>
    </div>
</div>
