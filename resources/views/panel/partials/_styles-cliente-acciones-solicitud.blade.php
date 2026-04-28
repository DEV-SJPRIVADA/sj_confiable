{{-- Reglas CSS puras (sin <style>): incluir dentro de un bloque <style> del layout. --}}
    .sol-cli-acciones--inactivos {
        opacity: 0.55;
        filter: grayscale(0.35);
        pointer-events: none;
    }
    .sol-cli-acciones-td .sol-cli-acc.sol-cli-acc--neutro,
    .sol-cli-acciones-td button.sol-cli-acc.sol-cli-acc--neutro,
    .sol-cli-acciones-td button.sol-cli-cancel-btn {
        width: 1.92rem;
        height: 1.92rem;
        min-width: 1.92rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.25rem;
        border: 1px solid #babfc5;
        background: linear-gradient(180deg, #fff 0%, #e9ecef 100%);
        color: #495057;
        font-size: 0.84rem;
        line-height: 1;
        cursor: pointer;
    }
    .sol-cli-acciones-td a.sol-cli-acc.sol-cli-acc--neutro {
        width: 1.92rem;
        height: 1.92rem;
        color: inherit;
    }
    .sol-cli-acciones-td a.sol-cli-acc--ver {
        width: 1.92rem;
        height: 1.92rem;
        min-width: 1.92rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.25rem;
        border: 1px solid #142a42;
        background: linear-gradient(180deg, #274a73 0%, #19365a 100%);
        color: #fff !important;
        font-size: 0.84rem;
    }
    .sol-cli-acciones-td a.sol-cli-acc--ver:hover {
        background: #0f2844 !important;
        filter: brightness(1.05);
    }
    .sol-cli-acciones-td .sol-cli-acc--clip {
        min-width: 3.25rem;
        height: 1.92rem;
        padding: 0 0.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.08rem;
        border-radius: 0.25rem;
        border: 1px solid #babfc5;
        background: linear-gradient(180deg, #fff 0%, #eef1f4 100%);
        color: #333;
        font-size: 0.75rem;
        text-decoration: none !important;
        line-height: 1;
    }
    .sol-cli-acciones-td a.sol-cli-acc--clip:hover {
        background: #e2e6ea;
        color: #111;
    }
    .sol-cli-acc-clip-icon { font-size: 0.76rem; opacity: 0.92; }
    .sol-cli-acc-clip-sep { color: #868e96; font-size: 0.7rem; transform: translateY(0.5px); }
    .sol-cli-acc-badge-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.05rem;
        height: 1.1rem;
        padding: 0 0.2rem;
        font-size: 0.63rem;
        font-weight: 800;
        color: #fff;
        background: #5b636a;
        border-radius: 999px;
        line-height: 1;
    }
