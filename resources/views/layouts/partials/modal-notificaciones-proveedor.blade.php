@php
    $notifSvc = app(\App\Services\Panel\NotificacionProveedorService::class);
    $notificaciones = $notificacionesProveedor ?? collect();
@endphp
<div class="modal fade" id="modalNotificacionesProveedor" tabindex="-1" aria-labelledby="modalNotificacionesProveedorLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-notificaciones-proveedor modal-dialog-scrollable">
        <div class="modal-content notificaciones-modal-prov-content border-0 shadow">
            <div class="modal-header notificaciones-modal-prov-header text-white border-0 py-2 px-3 rounded-top">
                <h2 class="modal-title notif-prov-title d-flex align-items-center gap-2 fw-bold mb-0 flex-grow-1" id="modalNotificacionesProveedorLabel">
                    <span class="notif-prov-head-ico rounded d-inline-flex align-items-center justify-content-center bg-white bg-opacity-25" aria-hidden="true"><i class="fas fa-bell fs-6"></i></span>
                    <span>Notificaciones</span>
                </h2>
                <button type="button" class="btn btn-sm btn-prov-notif-close d-inline-flex align-items-center justify-content-center border-0" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times text-white" aria-hidden="true"></i>
                </button>
            </div>
            @if ($notificaciones->isNotEmpty())
                <form method="post" action="{{ route('panel.proveedor.notificaciones.marcar-leidas') }}" class="border-0" id="formNotifProveedorMarcar">
                    @csrf
                    <div class="notificaciones-modal-prov-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input notif-prov-todos-check rounded-circle" type="checkbox" id="notifProvSelectAll" title="Seleccionar todos" autocomplete="off" aria-label="Seleccionar todos">
                            <label class="form-check-label notif-prov-toolbar-label text-body fw-medium" for="notifProvSelectAll">Seleccionar todos</label>
                        </div>
                        <button type="submit" class="btn btn-prov-marcar-leidas d-inline-flex align-items-center gap-2 text-uppercase">
                            <i class="fas fa-check-double" aria-hidden="true"></i>
                            Marcar como leídas
                        </button>
                    </div>
                </form>
            @endif
            <div class="notificaciones-modal-prov-lista flex-grow-1 overflow-auto px-2 py-3">
                @forelse ($notificaciones as $n)
                    <div class="notif-prov-item @if(! $n->leido) notif-prov-item--nueva @endif">
                        <div class="notif-prov-td notif-prov-td--check">
                                <input type="checkbox" class="form-check-input notif-prov-item-check m-0" form="formNotifProveedorMarcar" name="ids[]" value="{{ $n->id }}" autocomplete="off" title="Seleccionar" aria-label="Seleccionar notificación #{{ $n->id }}">
                        </div>
                        <div class="notif-prov-td notif-prov-td--ico" aria-hidden="true">
                            <span class="notif-prov-ico"><i class="fas fa-sync-alt"></i></span>
                        </div>
                        <div class="notif-prov-td notif-prov-td--texto min-w-0">
                            <span class="notif-prov-mensaje text-dark">{{ $n->mensaje }}</span>
                            <span class="notif-prov-fecha text-secondary"> ({{ $n->fecha?->format('d/m/Y H:i') ?? '—' }})</span>
                        </div>
                        <div class="notif-prov-td notif-prov-td--accion">
                            {{-- Marca leída y abre el mismo modal de detalle que en el listado de solicitudes (legado proveedor_solicitudes). --}}
                            <form method="post" action="{{ route('panel.proveedor.notificaciones.marcar-leidas') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="ids[]" value="{{ $n->id }}">
                                <input type="hidden" name="redirect_abrir_modal_solicitud" value="{{ (int) $n->id_solicitud }}">
                                <button type="submit" class="btn btn-sm btn-primary text-uppercase notif-prov-btn-detalle">ver detalle</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted notif-prov-vacio py-5 mb-0">No hay notificaciones sin leer.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<style>
    .modal-notificaciones-proveedor { max-width: min(36rem, 96vw); width: 100%; margin: 0.5rem auto; }
    .notificaciones-modal-prov-content { border-radius: 0.45rem; overflow: hidden; }
    .notificaciones-modal-prov-header {
        background: linear-gradient(90deg, #07355a 0%, #0d4d82 52%, #0a5aa0 100%);
    }
    .notif-prov-head-ico { width: 1.95rem; height: 1.95rem; }
    .notif-prov-title { font-size: 1.1rem; }
    .btn-prov-notif-close { background: rgba(255,255,255,0.15); width: 2.05rem; min-width: 2.05rem; height: 2.05rem; border-radius: 0.35rem !important; }
    .btn-prov-notif-close:hover { background: rgba(255,255,255,0.28); }
    .notificaciones-modal-prov-toolbar {
        border-bottom: 1px solid #d4dde6;
        background: linear-gradient(180deg, #e9f2fb 0%, #eaf0f8 100%);
    }
    .notif-prov-toolbar-label { font-size: 0.9rem; }
    .btn-prov-marcar-leidas {
        --bs-btn-color: #0d5a9e;
        --bs-btn-border-color: #6aaadb;
        --bs-btn-bg: #fff;
        --bs-btn-hover-bg: #eaf4fc;
        --bs-btn-hover-color: #0a4a7a;
        --bs-btn-hover-border-color: #4a90d4;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 0.45rem 0.85rem;
        line-height: 1.25;
        border: 2px solid #6aaadb !important;
    }
    .notificaciones-modal-prov-lista { max-height: 58vh; background: #fff; min-height: 8rem; }
    .notif-prov-item {
        display: grid;
        grid-template-columns: 1.4rem 2.4rem minmax(0, 1fr) auto;
        align-items: center;
        column-gap: 0.45rem;
        padding: 0.55rem 0.55rem;
        margin-bottom: 0.5rem;
        background: #f8fafc;
        border: 1px solid #d9e0e6;
        border-radius: 0.35rem;
    }
    .notif-prov-td--check { display: flex; align-items: flex-start; justify-content: center; padding-top: 0.1rem; }
    .notif-prov-td--ico { display: flex; align-items: center; justify-content: center; }
    .notif-prov-ico {
        display: flex; align-items: center; justify-content: center;
        width: 2.1rem; height: 2.1rem; border-radius: 50%;
        background: rgba(13, 90, 158, 0.14);
        color: #0d5a9e; font-size: 0.8rem; flex-shrink: 0;
    }
    .notif-prov-td--texto { line-height: 1.45; font-size: 0.9rem; }
    .notif-prov-fecha { font-size: 0.85em; white-space: nowrap; }
    .notif-prov-td--accion { display: flex; align-items: center; justify-content: flex-end; }
    .notif-prov-btn-detalle { font-size: 0.72rem; font-weight: 600; letter-spacing: 0.03em; white-space: nowrap; padding: 0.42rem 0.65rem; }
    .notif-prov-vacio { font-size: 0.95rem; color: #6c757d !important; }
    .notif-prov-item--nueva { box-shadow: inset 0 0 0 1px rgba(13, 90, 158, 0.18); background: #fff; }
    @media (max-width: 420px) {
        .notif-prov-item {
            grid-template-columns: 1.2rem 1.85rem minmax(0, 1fr);
            grid-template-rows: auto auto;
        }
        .notif-prov-td--accion { grid-column: 1 / -1; justify-content: flex-start; padding-left: 2.1rem; }
    }
    .notificaciones-modal-prov-lista::-webkit-scrollbar { width: 7px; }
    .notificaciones-modal-prov-lista::-webkit-scrollbar-thumb { background: #b8c6d6; border-radius: 4px; }
    .notificaciones-modal-prov-lista { scrollbar-color: #b8c6d6 #fff; scrollbar-width: thin; }
    #modalNotificacionesProveedor .form-check-input.notif-prov-todos-check { width: 1.1em; height: 1.1em; border-radius: 999px; border-width: 1.5px; }
    #modalNotificacionesProveedor .notif-prov-item-check { width: 1.1em; height: 1.1em; }
</style>
<script>
(function () {
    var m = document.getElementById('modalNotificacionesProveedor');
    if (!m) return;
    var all = document.getElementById('notifProvSelectAll');
    m.addEventListener('shown.bs.modal', function () {
        if (all) { all.indeterminate = false; all.checked = false; }
        m.querySelectorAll('.notif-prov-item-check').forEach(function (c) { c.checked = false; });
    });
    if (all) {
        all.addEventListener('change', function () {
            var on = this.checked;
            m.querySelectorAll('.notif-prov-item-check').forEach(function (c) { c.checked = on; });
        });
    }
})();
</script>
