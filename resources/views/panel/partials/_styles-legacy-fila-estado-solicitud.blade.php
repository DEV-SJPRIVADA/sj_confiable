    /* Colores de fila según estado (paridad sj_confiable1) */
    #tablaGestionSolicitudes tbody tr.fila-nuevo td,
    .panel-cliente-solicitudes-lista #tablaPanelSolicitudes tbody tr.fila-nuevo td,
    .prov-solic-legado-scope #tablaPanelSolicitudes tbody tr.fila-nuevo td {
        background-color: rgb(102, 115, 150) !important;
        color: #fff !important;
    }
    #tablaGestionSolicitudes tbody tr.fila-en-proceso td,
    .panel-cliente-solicitudes-lista #tablaPanelSolicitudes tbody tr.fila-en-proceso td,
    .prov-solic-legado-scope #tablaPanelSolicitudes tbody tr.fila-en-proceso td {
        background-color: rgba(255, 249, 196, 1) !important;
    }
    #tablaGestionSolicitudes tbody tr.fila-completado td,
    .panel-cliente-solicitudes-lista #tablaPanelSolicitudes tbody tr.fila-completado td,
    .prov-solic-legado-scope #tablaPanelSolicitudes tbody tr.fila-completado td {
        background-color: rgba(200, 230, 201, 1) !important;
        color: #2e7d32 !important;
    }
    #tablaGestionSolicitudes tbody tr.fila-cancelado td,
    .panel-cliente-solicitudes-lista #tablaPanelSolicitudes tbody tr.fila-cancelado td,
    .prov-solic-legado-scope #tablaPanelSolicitudes tbody tr.fila-cancelado td {
        background-color: rgba(255, 204, 188, 1) !important;
        color: #d84315 !important;
    }
    .fila-nuevo td { color: #fff !important; }

    /* Enlaces ID — gestión consultor */
    #tablaGestionSolicitudes tbody tr.fila-nuevo td a.solicitud-gestion-id-link {
        color: #fff !important;
    }
    #tablaGestionSolicitudes tbody tr.fila-en-proceso td a.solicitud-gestion-id-link {
        color: #212529 !important;
    }
    #tablaGestionSolicitudes tbody tr.fila-completado td a.solicitud-gestion-id-link {
        color: #2e7d32 !important;
    }
    #tablaGestionSolicitudes tbody tr.fila-cancelado td a.solicitud-gestion-id-link {
        color: #d84315 !important;
    }

    /* Enlaces ID — listado cliente */
    .panel-cliente-solicitudes-lista tbody tr.fila-nuevo td a.text-decoration-none {
        color: #fff !important;
    }
    .panel-cliente-solicitudes-lista tbody tr.fila-en-proceso td a.text-decoration-none {
        color: #0a58ca !important;
    }
    .panel-cliente-solicitudes-lista tbody tr.fila-completado td a.text-decoration-none {
        color: #2e7d32 !important;
    }
    .panel-cliente-solicitudes-lista tbody tr.fila-cancelado td a.text-decoration-none {
        color: #d84315 !important;
    }
