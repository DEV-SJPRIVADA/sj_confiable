@extends('layouts.app')

@section('title', 'Solicitud #'.$solicitud->id)

@push('styles')
<style>
    .solicitud-gestion-btn-aux {
        background: #f8f9fa;
        border: 1px solid #dee2e6 !important;
        color: #0d3a66;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
    }
    .solicitud-gestion-btn-aux:hover { background: #fff; color: #062a58; }
    .solicitud-gestion-volver {
        color: #2c3e50;
        border: 1px solid #ced4da;
        background: #fff;
    }
    .solicitud-gestion-volver:hover { background: #f8f9fa; color: #0b2239; }
    @media (min-width: 576px) {
        .solicitud-gestion__btn-enviar { max-width: 22rem; }
    }
    .solicitud-modal-detalle__bar {
        background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%) !important;
    }
    .solicitud-oc-gestion .card { border: 1px solid #e9ecef; }
    .solicitud-nueva-respuesta textarea { min-height: 7rem; }
    .solicitud-gestion-hero__grid {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 0.5rem 0.25rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 575.98px) {
        .solicitud-gestion-hero__grid { grid-template-columns: 1fr; text-align: center; }
        .solicitud-gestion-hero__grid .solicitud-gestion-hero__l { justify-content: center; }
        .solicitud-gestion-hero__grid .solicitud-gestion-hero__r { justify-content: center; }
    }
    .consultor-sol-principal-pdf-frame {
        width: 100%;
        min-height: 32rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background: #f8f9fa;
    }
</style>
@endpush

@section('content')
@php
    use App\Domain\Enums\HistorialRespuestaCanal;
    use Illuminate\Support\Facades\Storage;

    $st = (string) ($solicitud->estado ?? 'Nuevo');
    $solicitudCerradaGestión = str_contains(mb_strtolower($st), 'complet') || str_contains(mb_strtolower($st), 'cancel');
    $etiquetaEstado = function (string $clave): string {
        return match ($clave) {
            'En proceso' => 'En Proceso',
            'Nuevo' => 'Nuevo',
            'Completado' => 'Completado',
            'Cancelado' => 'Cancelado',
            default => $clave,
        };
    };
    $estadoOpciones = [
        'Nuevo' => 'Nuevo', 'En proceso' => 'En Proceso', 'Completado' => 'Completado', 'Cancelado' => 'Cancelado',
    ];
    if ($st !== '' && ! array_key_exists($st, $estadoOpciones)) {
        $estadoOpciones = [$st => $etiquetaEstado($st)] + $estadoOpciones;
    }

    $ultimaRespuestaClienteSj = $solicitud->historialRespuestas
        ->filter(fn ($h) => $h->canal === HistorialRespuestaCanal::ClienteSj)
        ->sortByDesc('fecha_respuesta')
        ->first();
    $textoUltimaRespuestaCliente = $ultimaRespuestaClienteSj !== null
        ? trim((string) $ultimaRespuestaClienteSj->respuesta)
        : '';

    $urlDocPrincipalConsultor = static function (string $ruta): string {
        $r = ltrim($ruta, '/');
        if ($r === '') {
            return '#';
        }
        if (str_starts_with($r, 'uploads/')) {
            return Storage::disk('public')->url($r);
        }

        return Storage::disk('public')->url('uploads/'.$r);
    };

    $docsAdjuntosPrincipal = collect();
    foreach ($solicitud->documentos as $d) {
        $docsAdjuntosPrincipal->push([
            'tipo' => 'solicitud',
            'id' => (int) $d->id,
            'nombre' => (string) ($d->nombre_documento ?? '—'),
            'url' => $urlDocPrincipalConsultor((string) ($d->ruta_documento ?? '')),
            'etiqueta' => 'Documento solicitud',
            'ref' => 'doc-'.((int) $d->id),
        ]);
    }
    foreach ($solicitud->respuestasMadre as $rm) {
        foreach ($rm->documentos as $dr) {
            $docsAdjuntosPrincipal->push([
                'tipo' => 'respuesta',
                'id' => (int) $dr->id,
                'nombre' => (string) ($dr->nombre_documentoResp ?? '—'),
                'url' => $urlDocPrincipalConsultor((string) ($dr->ruta_documentoResp ?? '')),
                'etiqueta' => 'Respuesta operativa (asociado)',
                'ref' => 'dresp-'.((int) $dr->id),
            ]);
        }
    }

    $orgClienteNombre = (string) ($solicitud->creador?->cliente?->razon_social ?? '—');
    $oldAdjNotif = old('adjuntos_notificacion');
@endphp
<div class="solicitud-gestion-hero__grid">
    <div class="d-flex align-items-center gap-2 flex-wrap solicitud-gestion-hero__l">
        <a href="{{ route('panel.consultor.solicitudes.index') }}" class="btn btn-sm solicitud-gestion-volver d-inline-flex align-items-center text-uppercase shadow-sm" title="Volver al listado de solicitudes" aria-label="Volver al listado de solicitudes">
            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
            <span>Volver</span>
        </a>
        <button type="button" class="btn btn-sm solicitud-gestion-btn-aux shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#ocDetalleSolicitud" aria-controls="ocDetalleSolicitud">
            <i class="fas fa-info-circle me-1" aria-hidden="true"></i>Detalle
        </button>
    </div>
    <h1 class="h4 mb-0">Solicitud #{{ $solicitud->id }}</h1>
    <div class="d-flex justify-content-end solicitud-gestion-hero__r">
        <button type="button" class="btn btn-sm solicitud-gestion-btn-aux shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#ocHistorialSolicitud" aria-controls="ocHistorialSolicitud">
            <i class="fas fa-history me-1" aria-hidden="true"></i>Historial
        </button>
    </div>
</div>

@can('manageAsConsultor', $solicitud)
@if ($solicitudCerradaGestión)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white text-primary fw-bold">Última respuesta registrada (solicitud cerrada)</div>
    <div class="card-body solicitud-nueva-respuesta">
        <p class="text-muted small mb-3">La solicitud está en estado final; no se permiten nuevos mensajes ni movimientos.</p>
        <label class="form-label fw-semibold">Respuesta visible para la organización cliente</label>
        <textarea class="form-control bg-body-secondary" rows="6" readonly>{{ $textoUltimaRespuestaCliente !== '' ? $textoUltimaRespuestaCliente : '—' }}</textarea>
    </div>
</div>
@else
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white text-primary fw-bold">Nueva respuesta</div>
    <div class="card-body solicitud-nueva-respuesta">
        <p class="text-muted small mb-3 mb-md-4">Escriba el mensaje que verá la organización cliente en el historial. Los PDF opcionales se guardan en el expediente. Marque qué adjuntos existentes desea mencionar en el aviso al cliente; los PDF que cargue en este envío siempre se incluyen en ese aviso.</p>
        @if ($errors->any())
            <div class="alert alert-danger small mb-3" role="alert">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('panel.consultor.solicitudes.respuesta', $solicitud) }}" enctype="multipart/form-data" id="formNuevaRespuestaConsultor">
            @csrf
            <div class="mb-3">
                <label for="nueva_respuesta_borrador" class="form-label fw-semibold">Respuesta</label>
                <textarea class="form-control @error('nueva_respuesta') is-invalid @enderror" id="nueva_respuesta_borrador" name="nueva_respuesta" rows="5" maxlength="20000" required placeholder="Informe aquí la respuesta o actualización">{{ old('nueva_respuesta') }}</textarea>
                @error('nueva_respuesta')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <input type="hidden" name="visible_para_cliente" value="0">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="visible_para_cliente" value="1" id="consultor_visible_para_cliente" @checked(old('visible_para_cliente', '1') !== '0')>
                <label class="form-check-label small" for="consultor_visible_para_cliente">Mostrar al cliente en su historial y enviar notificación a su organización (desmarcar sólo para trámite interno / hacia el asociado)</label>
            </div>
            @if ($docsAdjuntosPrincipal->isNotEmpty())
            <div class="mb-3 p-3 rounded border bg-light">
                <span class="form-label fw-semibold d-block">Adjuntos ya en expediente — incluir en el aviso al cliente</span>
                <p class="small text-muted mb-2">Desmarque los que no deba ver mencionados en la notificación (el archivo permanece en el sistema).</p>
                @foreach ($docsAdjuntosPrincipal as $docRow)
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="adjuntos_notificacion[]" value="{{ $docRow['ref'] }}" id="notif-{{ $docRow['ref'] }}" @checked($oldAdjNotif === null ? true : in_array($docRow['ref'], (array) $oldAdjNotif, true))>
                        <label class="form-check-label small" for="notif-{{ $docRow['ref'] }}">{{ $docRow['nombre'] }} <span class="text-muted">({{ $docRow['tipo'] === 'solicitud' ? 'solicitud' : 'asociado' }})</span></label>
                    </div>
                @endforeach
                @error('adjuntos_notificacion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('adjuntos_notificacion.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            @endif
            <div class="mb-3">
                <span class="form-label fw-semibold d-block">Documentos adjuntos (Máx. 10 PDFs)</span>
                <div class="input-group">
                    <input type="file" class="form-control @error('documentos') is-invalid @enderror @error('documentos.*') is-invalid @enderror" id="consultor_adjuntos_pdf" name="documentos[]" accept=".pdf,application/pdf" multiple aria-label="Documentos adjuntos PDF">
                </div>
                @error('documentos')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('documentos.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <p class="text-muted small mt-1 mb-0" id="consultorAdjuntosCuenta">Archivos seleccionados: 0/10</p>
            </div>
            <div class="mb-4">
                <label for="nuevo_estado_solicitud" class="form-label fw-semibold">Nuevo estado</label>
                <select id="nuevo_estado_solicitud" name="nuevo_estado" class="form-select @error('nuevo_estado') is-invalid @enderror" required>
                    @foreach ($estadoOpciones as $val => $label)
                        <option value="{{ $val }}" @selected(old('nuevo_estado', $st) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('nuevo_estado')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="d-grid gap-2 mx-auto solicitud-gestion__btn-enviar">
                <button type="submit" class="btn btn-primary btn-lg text-uppercase fw-semibold py-2" id="btnEnviarRespuestaConsultor">Enviar respuesta</button>
            </div>
        </form>
    </div>
</div>
@endif
@endcan

@can('assignToProveedor', $solicitud)
    @php
        $clienteFinalVal = (string) old('cliente_final', (string) ($solicitud->cliente_final ?? ''));
    @endphp
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white text-primary fw-bold">Asignación de asociado de negocio</div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif
            <p class="text-muted small mb-3">Elija el asociado; el cambio se registra en el historial operativo y se notifica solo a usuarios del asociado asignado (el cliente no recibe aviso en esta acción).</p>
            <form method="post" action="{{ route('panel.consultor.solicitudes.asignar', $solicitud) }}" id="formAsignarProveedorConsultor">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="cliente_final" class="form-label fw-semibold">Cliente final</label>
                        <input type="text" class="form-control @if(trim($clienteFinalVal) !== '') bg-body-secondary @endif" name="cliente_final" id="cliente_final" maxlength="150" value="{{ $clienteFinalVal }}" inputmode="text" autocomplete="off" placeholder="(Opcional)">
                    </div>
                    <div class="col-md-6">
                        <label for="tipo_cliente" class="form-label fw-semibold">Tipo cliente</label>
                        <select name="tipo_cliente" id="tipo_cliente" class="form-select">
                            <option value="">— N/C —</option>
                            <option value="Interno" @selected(old('tipo_cliente', $solicitud->tipo_cliente) === 'Interno')>Interno</option>
                            <option value="Externo" @selected(old('tipo_cliente', $solicitud->tipo_cliente) === 'Externo')>Externo</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="id_proveedor" class="form-label fw-semibold">Asociado de negocio</label>
                    <select name="id_proveedor" id="id_proveedor" class="form-select" required>
                        <option value="">Seleccione un asociado de negocio…</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id_proveedor }}" @selected((int) $solicitud->id_proveedor === (int) $p->id_proveedor)>
                                {{ $p->razon_social_proveedor }} ({{ $p->nombre_comercial }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="comentario_asignacion" class="form-label fw-semibold">Mensaje para el asociado (opcional)</label>
                    <textarea
                        name="comentario_asignacion"
                        id="comentario_asignacion"
                        class="form-control"
                        rows="4"
                        maxlength="2000"
                        placeholder="Instrucciones o contexto para el asociado de negocio. Se verá en el historial operativo y en la notificación."
                    >{{ old('comentario_asignacion') }}</textarea>
                    <div class="form-text">Queda registrado en el historial de la solicitud y se incluye en el aviso al asociado.</div>
                    @error('comentario_asignacion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex flex-wrap">
                    <button type="submit" class="btn btn-outline-primary text-uppercase fw-semibold px-4 d-inline-flex align-items-center gap-2">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                        <span>Asignar y procesar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endcan

<div id="consultor-main-documentos" class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Documentos adjuntos (respuesta principal)</div>
    <div class="card-body">
        @if ($docsAdjuntosPrincipal->isEmpty())
            <p class="text-muted small mb-0">Sin archivos adjuntos en la respuesta principal.</p>
        @else
            @foreach ($docsAdjuntosPrincipal as $item)
                <div class="mb-4 @if (! $loop->last) pb-4 border-bottom @endif">
                    <div class="small fw-medium mb-1">{{ $item['nombre'] }}</div>
                    <div class="small text-muted mb-2" style="font-size: 0.72rem; letter-spacing: 0.04em;">{{ strtoupper($item['etiqueta']) }}</div>
                    @if ($item['url'] !== '#')
                        <iframe class="consultor-sol-principal-pdf-frame mb-3" title="Vista previa {{ $item['nombre'] }}" src="{{ $item['url'] }}#toolbar=1"></iframe>
                    @else
                        <p class="text-muted small mb-2">Vista previa no disponible para este archivo.</p>
                    @endif
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        @if ($item['url'] !== '#')
                            <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm text-uppercase fw-semibold">Abrir en nueva pestaña</a>
                        @endif
                        @if ($item['tipo'] === 'solicitud')
                            @can('deleteAdjuntoExpedienteAsConsultor', $solicitud)
                                <form method="post" action="{{ route('panel.consultor.solicitudes.documentos.destroy', [$solicitud, $item['id']]) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este documento? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm text-uppercase fw-semibold">Eliminar</button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

@if (! $solicitudCerradaGestión)
<div class="modal fade" id="modalConfirmarRespuestaConsultor" tabindex="-1" aria-labelledby="modalConfirmarRespuestaConsultorTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="modalConfirmarRespuestaConsultorTitulo">Confirmar envío</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body small">
                <p class="mb-2"><strong>Destinatario del aviso:</strong> <span id="modalConsultorDestinatario"></span></p>
                <p class="mb-2"><strong>Visible en panel cliente:</strong> <span id="modalConsultorVisibleCliente"></span></p>
                <p class="mb-2"><strong>Nuevo estado:</strong> <span id="modalConsultorEstado"></span></p>
                <p class="mb-2"><strong>Mensaje (extracto):</strong></p>
                <div class="border rounded p-2 bg-light text-break mb-2" id="modalConsultorMensaje"></div>
                <p class="mb-1"><strong>Documentos mencionados en el aviso:</strong></p>
                <ul class="mb-0 ps-3" id="modalConsultorAdjuntos"></ul>
            </div>
            <div class="modal-footer flex-nowrap gap-2">
                <button type="button" class="btn btn-outline-secondary text-uppercase fw-semibold" data-bs-dismiss="modal">Editar</button>
                <button type="button" class="btn btn-primary text-uppercase fw-semibold" id="modalConsultorBtnAceptar">Aceptar</button>
            </div>
        </div>
    </div>
</div>
@endif

@can('assignToProveedor', $solicitud)
@if (! $solicitudCerradaGestión)
<div class="modal fade" id="modalConfirmarAsignacionConsultor" tabindex="-1" aria-labelledby="modalConfirmarAsignacionConsultorTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="modalConfirmarAsignacionConsultorTitulo">Confirmar asignación</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body small">
                <p class="mb-2"><strong>Solicitud:</strong> #{{ $solicitud->id }}</p>
                <p class="mb-2"><strong>Cliente final:</strong> <span id="modalAsignClienteFinal">—</span></p>
                <p class="mb-2"><strong>Tipo cliente:</strong> <span id="modalAsignTipoCliente">—</span></p>
                <p class="mb-2"><strong>Asociado de negocio:</strong> <span id="modalAsignProveedor">—</span></p>
                <p class="mb-0 text-muted">La solicitud quedará en <strong>En proceso</strong>. Se notificará al asociado en su panel; la organización cliente <strong>no</strong> recibe aviso en esta acción.</p>
            </div>
            <div class="modal-footer flex-nowrap gap-2">
                <button type="button" class="btn btn-outline-secondary text-uppercase fw-semibold" data-bs-dismiss="modal">Editar</button>
                <button type="button" class="btn btn-primary text-uppercase fw-semibold" id="modalAsignBtnAceptar">Aceptar</button>
            </div>
        </div>
    </div>
</div>
@endif
@endcan

@include('panel.consultor.solicitudes._offcanvases-gestion-solicitud', ['solicitud' => $solicitud])
@endsection

@push('scripts')
<script>
(function () {
    function abrirYHash() {
        const h = window.location.hash;
        if (h === '#consultor-main-documentos') {
            var docMain = document.getElementById('consultor-main-documentos');
            if (docMain) {
                docMain.scrollIntoView({ block: 'start', behavior: 'smooth' });
            }
            return;
        }
        if (h === '#documentos' || h === '#panel-solicitud-documentos') {
            const el = document.getElementById('ocDetalleSolicitud');
            if (el) {
                const oc = window.bootstrap.Offcanvas.getOrCreateInstance(el);
                oc.show();
                setTimeout(function () {
                    var t = document.getElementById('oc-documentos');
                    if (t) { t.scrollIntoView({ block: 'start', behavior: 'smooth' }); }
                }, 400);
            }
            return;
        }
        if (h === '#historial' || h === '#panel-solicitud-historial') {
            var ocr = document.getElementById('ocHistorialSolicitud');
            if (ocr) { window.bootstrap.Offcanvas.getOrCreateInstance(ocr).show(); }
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        abrirYHash();
        var fi = document.getElementById('consultor_adjuntos_pdf');
        var cnt = document.getElementById('consultorAdjuntosCuenta');
        if (fi && cnt) {
            fi.addEventListener('change', function () {
                var n = fi.files ? fi.files.length : 0;
                if (n > 10) {
                    cnt.textContent = 'Máximo 10 archivos. Se usarán sólo los primeros 10 al enviar.';
                    cnt.classList.add('text-warning');
                } else {
                    cnt.textContent = 'Archivos seleccionados: ' + n + '/10';
                    cnt.classList.remove('text-warning');
                }
            });
        }

        var form = document.getElementById('formNuevaRespuestaConsultor');
        var modalEl = document.getElementById('modalConfirmarRespuestaConsultor');
        if (form && modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var orgCliente = @json($orgClienteNombre);
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            var confirmedOnce = false;
            var pendingConfirmSubmit = false;
            form.addEventListener('submit', function (e) {
                if (confirmedOnce) {
                    confirmedOnce = false;
                    return;
                }
                e.preventDefault();
                var ta = document.getElementById('nueva_respuesta_borrador');
                var sel = document.getElementById('nuevo_estado_solicitud');
                var msg = ta ? String(ta.value).trim() : '';
                var dest = document.getElementById('modalConsultorDestinatario');
                var est = document.getElementById('modalConsultorEstado');
                var msgEl = document.getElementById('modalConsultorMensaje');
                var ul = document.getElementById('modalConsultorAdjuntos');
                if (!dest || !est || !msgEl || !ul) {
                    return;
                }
                dest.textContent = orgCliente;
                var visRow = document.getElementById('modalConsultorVisibleCliente');
                var visCb = document.getElementById('consultor_visible_para_cliente');
                if (visRow) {
                    visRow.textContent = visCb && visCb.checked ? 'Sí (historial cliente + notificación)' : 'No (sólo auditoría SJ)';
                }
                est.textContent = sel && sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : '—';
                var extracto = msg.length > 320 ? msg.slice(0, 320) + '…' : msg;
                msgEl.textContent = extracto || '—';
                ul.innerHTML = '';
                form.querySelectorAll('input[name="adjuntos_notificacion[]"]:checked').forEach(function (cb) {
                    var li = document.createElement('li');
                    var lbl = form.querySelector('label[for="' + cb.id + '"]');
                    li.textContent = lbl ? lbl.textContent.replace(/\s+/g, ' ').trim() : cb.value;
                    ul.appendChild(li);
                });
                if (fi && fi.files) {
                    for (var i = 0; i < fi.files.length; i++) {
                        var liN = document.createElement('li');
                        liN.textContent = fi.files[i].name + ' (nuevo en este envío)';
                        ul.appendChild(liN);
                    }
                }
                if (!ul.children.length) {
                    var li0 = document.createElement('li');
                    li0.textContent = 'Ninguno';
                    ul.appendChild(li0);
                }
                modal.show();
            });
            var btnAceptar = document.getElementById('modalConsultorBtnAceptar');
            if (btnAceptar) {
                btnAceptar.addEventListener('click', function () {
                    pendingConfirmSubmit = true;
                    modal.hide();
                });
            }
            modalEl.addEventListener('hidden.bs.modal', function () {
                if (!pendingConfirmSubmit) {
                    return;
                }
                pendingConfirmSubmit = false;
                confirmedOnce = true;
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            });
        }

        var formAsign = document.getElementById('formAsignarProveedorConsultor');
        var modalAsignEl = document.getElementById('modalConfirmarAsignacionConsultor');
        if (formAsign && modalAsignEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modalAsign = bootstrap.Modal.getOrCreateInstance(modalAsignEl);
            var confirmedAsign = false;
            var pendingAsign = false;
            formAsign.addEventListener('submit', function (e) {
                if (confirmedAsign) {
                    confirmedAsign = false;
                    return;
                }
                e.preventDefault();
                if (!formAsign.checkValidity()) {
                    formAsign.reportValidity();
                    return;
                }
                var inpCf = document.getElementById('cliente_final');
                var selTipo = document.getElementById('tipo_cliente');
                var selProv = document.getElementById('id_proveedor');
                var elCf = document.getElementById('modalAsignClienteFinal');
                var elTipo = document.getElementById('modalAsignTipoCliente');
                var elProv = document.getElementById('modalAsignProveedor');
                if (!elCf || !elTipo || !elProv) {
                    return;
                }
                var cf = inpCf && String(inpCf.value).trim() !== '' ? String(inpCf.value).trim() : '—';
                elCf.textContent = cf;
                elTipo.textContent = selTipo && selTipo.selectedOptions[0] ? selTipo.selectedOptions[0].textContent.trim() : '—';
                elProv.textContent = selProv && selProv.selectedOptions[0] ? selProv.selectedOptions[0].textContent.trim() : '—';
                modalAsign.show();
            });
            var btnAsignAceptar = document.getElementById('modalAsignBtnAceptar');
            if (btnAsignAceptar) {
                btnAsignAceptar.addEventListener('click', function () {
                    pendingAsign = true;
                    modalAsign.hide();
                });
            }
            modalAsignEl.addEventListener('hidden.bs.modal', function () {
                if (!pendingAsign) {
                    return;
                }
                pendingAsign = false;
                confirmedAsign = true;
                if (typeof formAsign.requestSubmit === 'function') {
                    formAsign.requestSubmit();
                } else {
                    formAsign.submit();
                }
            });
        }
    });
    document.querySelectorAll('[data-oc-scroll-doc]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            var t = document.getElementById('oc-documentos');
            if (t) { t.scrollIntoView({ block: 'start', behavior: 'smooth' }); }
        });
    });
})();
</script>
@endpush

