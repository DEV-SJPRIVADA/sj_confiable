@php
    $notifSvc = app(\App\Services\Panel\NotificacionClienteService::class);
    $notificaciones = $notificacionesCliente ?? collect();
@endphp
<div class="modal fade" id="modalNotificacionesCliente" tabindex="-1" aria-labelledby="modalNotificacionesClienteLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-notificaciones-cliente modal-dialog-scrollable">
        <div class="modal-content notificaciones-modal-cli-content border-0">
            <div class="modal-header notificaciones-modal-cli-header text-white border-0 py-2 px-3">
                <h2 class="modal-title notif-cli-modal-title d-flex align-items-center gap-2 fw-bold mb-0" id="modalNotificacionesClienteLabel">
                    <i class="fas fa-bell" aria-hidden="true"></i> Notificaciones
                </h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="post" action="{{ route('panel.cliente.notificaciones.marcar-leidas') }}" class="d-flex flex-column notificaciones-modal-cli-form" id="formNotifCliente">
                @csrf
                @if ($notificaciones->isNotEmpty())
                <div class="notificaciones-modal-cli-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-2 bg-white">
                    <div class="form-check mb-0">
                        <input class="form-check-input notif-cli-todos-check rounded-circle" type="checkbox" id="notifCliSelectAll" title="Seleccionar todos" aria-label="Seleccionar todos">
                        <label class="form-check-label notif-cli-toolbar-label text-body" for="notifCliSelectAll">Seleccionar todos</label>
                    </div>
                    <button type="submit" class="btn btn-cli-marcar-leidas d-inline-flex align-items-center gap-1 text-uppercase">
                        <i class="fas fa-check" aria-hidden="true"></i>
                        Marcar como leídas
                    </button>
                </div>
                @endif
                <div class="notificaciones-modal-cli-lista flex-grow-1 overflow-auto px-2 py-2">
                    @forelse ($notificaciones as $n)
                        <div class="notif-cli-item @if(! $n->leido) notif-cli-item--nueva @endif">
                            <div class="notif-cli-td notif-cli-td--check">
                                <input type="checkbox" class="form-check-input notif-cli-item-check m-0" name="ids[]" value="{{ $n->id }}" title="Seleccionar" aria-label="Seleccionar notificación #{{ $n->id }}">
                            </div>
                            <div class="notif-cli-td notif-cli-td--ico" aria-hidden="true">
                                <span class="notif-cli-ico"><i class="fas fa-info-circle"></i></span>
                            </div>
                            <div class="notif-cli-td notif-cli-td--texto min-w-0">
                                <span class="notif-cli-mensaje text-dark">{{ $n->mensaje }}</span>
                                <span class="notif-cli-fecha text-secondary"> ({{ $n->fecha?->format('d/m/Y H:i') ?? '—' }})</span>
                            </div>
                            <div class="notif-cli-td notif-cli-td--accion">
                                <a class="btn btn-sm btn-primary text-uppercase notif-cli-btn-detalle" href="{{ $notifSvc->urlDetalle($n) }}">ver estado</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted notif-cli-vacio py-4 mb-0">No hay notificaciones sin leer.</p>
                    @endforelse
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .modal-notificaciones-cliente { max-width: min(36rem, 96vw); width: 100%; margin: 0.5rem auto; }
    .notificaciones-modal-cli-content { border-radius: 0.4rem; overflow: hidden; }
    .notificaciones-modal-cli-header {
        background: linear-gradient(90deg, #0d3d5c 0%, #156896 100%);
    }
    .notif-cli-modal-title { font-size: 1.1rem; }
    .notificaciones-modal-cli-toolbar { border-bottom: 1px solid #e2e6ea; }
    .notif-cli-toolbar-label { font-size: 0.9rem; }
    .btn-cli-marcar-leidas {
        --bs-btn-color: #0d5a7a;
        --bs-btn-border-color: #5aa3c4;
        --bs-btn-bg: #fff;
        --bs-btn-hover-bg: #f0f7fc;
        --bs-btn-hover-color: #0a4a62;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        padding: 0.45rem 0.7rem;
        line-height: 1.2;
    }
    .notificaciones-modal-cli-lista { max-height: 58vh; background: #eef1f4; }
    .notif-cli-item {
        display: grid;
        grid-template-columns: 1.4rem 2.4rem minmax(0, 1fr) auto;
        align-items: center;
        column-gap: 0.4rem;
        padding: 0.55rem 0.5rem 0.55rem 0.4rem;
        margin-bottom: 0.5rem;
        background: #fff;
        border: 1px solid #d9e0e6;
        border-radius: 0.35rem;
    }
    .notif-cli-td--check { display: flex; align-items: flex-start; justify-content: center; padding-top: 0.1rem; }
    .notif-cli-td--ico { display: flex; align-items: center; justify-content: center; }
    .notif-cli-ico {
        display: flex; align-items: center; justify-content: center;
        width: 2.1rem; height: 2.1rem; border-radius: 50%;
        background: rgba(21, 104, 150, 0.12);
        color: #156896; font-size: 0.8rem; flex-shrink: 0;
    }
    .notif-cli-td--texto { line-height: 1.4; font-size: 0.9rem; }
    .notif-cli-fecha { font-size: 0.88em; white-space: nowrap; }
    .notif-cli-td--accion { display: flex; align-items: center; justify-content: flex-end; }
    .notif-cli-btn-detalle { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.02em; white-space: nowrap; padding: 0.4rem 0.6rem; }
    .notif-cli-vacio { font-size: 0.9rem; }
    .notif-cli-item--nueva { box-shadow: inset 0 0 0 1px rgba(21, 104, 150, 0.12); }
    @media (max-width: 420px) {
        .notif-cli-item {
            grid-template-columns: 1.2rem 1.85rem minmax(0, 1fr);
            grid-template-rows: auto auto;
        }
        .notif-cli-td--accion { grid-column: 1 / -1; justify-content: flex-start; padding-left: 2.1rem; }
    }
    .notificaciones-modal-cli-lista::-webkit-scrollbar { width: 6px; }
    .notificaciones-modal-cli-lista::-webkit-scrollbar-thumb { background: #c4ccd4; border-radius: 3px; }
    .notificaciones-modal-cli-lista { scrollbar-color: #c4ccd4 #eef1f4; scrollbar-width: thin; }
    .form-check .notif-cli-todos-check { width: 1.1em; height: 1.1em; border-radius: 0.2rem; }
    #modalNotificacionesCliente .notif-cli-item-check { width: 1.1em; height: 1.1em; }
</style>
<script>
(function () {
    var m = document.getElementById('modalNotificacionesCliente');
    if (!m) return;
    var all = document.getElementById('notifCliSelectAll');
    m.addEventListener('shown.bs.modal', function () {
        if (all) { all.indeterminate = false; all.checked = false; }
        m.querySelectorAll('.notif-cli-item-check').forEach(function (c) { c.checked = false; });
    });
    if (all) {
        all.addEventListener('change', function () {
            var on = this.checked;
            m.querySelectorAll('.notif-cli-item-check').forEach(function (c) { c.checked = on; });
        });
    }
})();
</script>
