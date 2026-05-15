{{-- Confirmación de anulación (sustituye confirm() del navegador). --}}
<div class="modal fade" id="modalAnularSolicitudCliente" tabindex="-1" aria-labelledby="modalAnularSolicitudClienteLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-anular-solicitud-cli">
        <div class="modal-content modal-anular-solicitud-cli__content border-0 shadow">
            <div class="modal-header modal-anular-solicitud-cli__header text-white border-0 py-3 px-4">
                <h2 class="modal-title h5 d-flex align-items-center gap-2 fw-bold mb-0" id="modalAnularSolicitudClienteLabel">
                    <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                    <span>Anular solicitud</span>
                </h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body px-4 py-4 text-center">
                <p class="mb-1 fw-semibold text-dark" id="modalAnularSolicitudClientePregunta">¿Anular esta solicitud?</p>
                <p class="mb-0 small text-muted" id="modalAnularSolicitudClienteDetalle">Esta acción no se puede deshacer. El equipo SJ será notificado.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4 pt-0 px-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">No, volver</button>
                <button type="button" class="btn btn-danger px-4 fw-semibold" id="modalAnularSolicitudClienteConfirmar">
                    Sí, anular solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-anular-solicitud-cli { max-width: min(28rem, 92vw); }
    .modal-anular-solicitud-cli__content { border-radius: 0.45rem; overflow: hidden; }
    .modal-anular-solicitud-cli__header {
        background: linear-gradient(180deg, #0c4a8a 0%, #083060 100%);
    }
</style>
@push('scripts')
<script>
(function () {
    var modalEl = document.getElementById('modalAnularSolicitudCliente');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    var pregunta = document.getElementById('modalAnularSolicitudClientePregunta');
    var btnConfirmar = document.getElementById('modalAnularSolicitudClienteConfirmar');
    var formPendiente = null;
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-anular-solicitud-trigger]');
        if (!btn) return;
        e.preventDefault();
        var formId = btn.getAttribute('data-anular-form-id');
        if (!formId) return;
        formPendiente = document.getElementById(formId);
        if (!formPendiente) return;
        var idSol = btn.getAttribute('data-solicitud-id') || '';
        if (pregunta) {
            pregunta.textContent = idSol
                ? '¿Anular la solicitud #' + idSol + '?'
                : '¿Anular esta solicitud?';
        }
        modal.show();
    });

    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', function () {
            if (formPendiente) {
                formPendiente.submit();
                formPendiente = null;
            }
            modal.hide();
        });
    }

    modalEl.addEventListener('hidden.bs.modal', function () {
        formPendiente = null;
    });
})();
</script>
@endpush
