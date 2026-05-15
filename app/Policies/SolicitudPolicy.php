<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Enums\UserRole;
use App\Models\Solicitud;
use App\Models\Usuario;

/**
 * Reglas alineadas al flujo: cliente nunca trata con el asociado de negocios de forma directa
 * (la mediación es por consultor SJ; la comprobación de “canal” en mensajes/respuestas
 * se refuerza al portar módulos de notificaciones y respuestas).
 */
class SolicitudPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return UserRole::tryFrom((int) $usuario->id_rol) !== null;
    }

    public function create(Usuario $actor): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);
        if ($rol === null || $actor->id_cliente === null) {
            return false;
        }
        if ($rol === UserRole::ClienteSinPermisos) {
            return false;
        }
        if (! in_array($rol, [UserRole::Cliente, UserRole::AdminCliente], true)) {
            return false;
        }

        return (bool) $actor->permiso_crear_solicitudes;
    }

    public function view(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);
        if ($rol === null) {
            return false;
        }

        return match ($rol) {
            UserRole::Admin, UserRole::SuperAdmin => true,
            UserRole::Proveedor => (int) $solicitud->id_proveedor === (int) $actor->id_proveedor
                && $actor->id_proveedor !== null,
            UserRole::Cliente, UserRole::AdminCliente, UserRole::ClienteSinPermisos => $this
                ->solicitudPerteneceAOrganizacionCliente($solicitud, $actor),
        };
    }

    /**
     * Cierre / estado final: solo personal SJ (consultor).
     */
    public function closeAsConsultor(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);

        return in_array($rol, [UserRole::Admin, UserRole::SuperAdmin], true) && $this->view($actor, $solicitud);
    }

    public function manageAsConsultor(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);

        return in_array($rol, [UserRole::Admin, UserRole::SuperAdmin], true) && $this->view($actor, $solicitud);
    }

    public function assignToProveedor(Usuario $actor, Solicitud $solicitud): bool
    {
        return $this->manageAsConsultor($actor, $solicitud)
            && ! $this->solicitudCerradaParaGestion($solicitud);
    }

    /**
     * Registrar mensaje / estado / adjuntos nuevos hacia la organización cliente (sólo con solicitud abierta).
     */
    public function registrarNuevaRespuestaConsultor(Usuario $actor, Solicitud $solicitud): bool
    {
        return $this->manageAsConsultor($actor, $solicitud)
            && ! $this->solicitudCerradaParaGestion($solicitud);
    }

    /**
     * Quitar PDFs de la tabla {@see Documento} (expediente de la solicitud). No aplica a {@see DocumentoRespuesta} del asociado.
     */
    public function deleteAdjuntoExpedienteAsConsultor(Usuario $actor, Solicitud $solicitud): bool
    {
        return $this->manageAsConsultor($actor, $solicitud)
            && ! $this->solicitudCerradaParaGestion($solicitud);
    }

    /**
     * El consultor SJ no elimina documentos operativos ya registrados por el asociado.
     */
    public function deleteDocumentoRespuestaOperativaAsConsultor(Usuario $actor, Solicitud $solicitud): bool
    {
        unset($actor, $solicitud);

        return false;
    }

    /**
     * Ícono «editar» en listado y detalle cliente (paridad legado: activa, no Completada ni Cancelada).
     */
    public function openClienteEdit(Usuario $actor, Solicitud $solicitud): bool
    {
        return $this->clientePuedeMostrarBotonEditarDetalle($actor, $solicitud);
    }

    /**
     * Guardar edición cliente: paridad legado (activa, no Completada ni Cancelada).
     */
    public function update(Usuario $actor, Solicitud $solicitud): bool
    {
        return $this->clientePuedeMostrarBotonEditarDetalle($actor, $solicitud);
    }

    /**
     * Anular solicitud (estado Cancelado / inactivo). Paridad legado: activa y no Completada.
     */
    public function cancel(Usuario $actor, Solicitud $solicitud): bool
    {
        return $this->clientePuedeCancelarSolicitud($actor, $solicitud);
    }

    /**
     * Adjuntar PDFs a la solicitud (tabla documentos): permiso legacy permiso_subir_documentos.
     */
    public function attachDocumentosCliente(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);
        if ($rol === null) {
            return false;
        }
        if ($actor->id_cliente === null) {
            return false;
        }
        if (! in_array($rol, [UserRole::Cliente, UserRole::AdminCliente], true)) {
            return false;
        }
        if (! (bool) $actor->permiso_subir_documentos) {
            return false;
        }
        if (! $this->view($actor, $solicitud)) {
            return false;
        }

        return trim(mb_strtolower((string) $solicitud->estado)) !== 'cancelado';
    }

    public function actAsProveedor(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);

        return $rol === UserRole::Proveedor
            && (int) $solicitud->id_proveedor === (int) $actor->id_proveedor
            && $actor->id_proveedor !== null;
    }

    /**
     * Respuesta operativa del asociado: {@see RespuestaMadre}, {@see DocumentoRespuesta} e historial {@see HistorialRespuestaCanal::SjProveedor}.
     */
    public function respondAsProveedor(Usuario $actor, Solicitud $solicitud): bool
    {
        if (! $this->actAsProveedor($actor, $solicitud)) {
            return false;
        }

        $est = mb_strtolower(trim((string) ($solicitud->estado ?? '')));
        if ($est === '' || str_contains($est, 'cancel')) {
            return false;
        }
        if (str_contains($est, 'complet')) {
            return false;
        }

        return true;
    }

    /**
     * Completado / Cancelado: sin nuevas respuestas, asignaciones ni borrado de expediente por consultor.
     */
    private function solicitudCerradaParaGestion(Solicitud $solicitud): bool
    {
        $est = mb_strtolower(trim((string) ($solicitud->estado ?? '')));

        return $est !== ''
            && (str_contains($est, 'complet') || str_contains($est, 'cancel'));
    }

    private function solicitudPerteneceAOrganizacionCliente(Solicitud $solicitud, Usuario $actor): bool
    {
        if ($actor->id_cliente === null) {
            return false;
        }

        $creador = $solicitud->relationLoaded('creador') ? $solicitud->creador : $solicitud->creador()->first();
        if ($creador === null) {
            return false;
        }

        return (int) $creador->id_cliente === (int) $actor->id_cliente;
    }

    private function clientePuedeMostrarBotonEditarDetalle(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);
        if ($rol === null) {
            return false;
        }
        if ($actor->id_cliente === null) {
            return false;
        }
        if (! in_array($rol, [UserRole::Cliente, UserRole::AdminCliente], true)) {
            return false;
        }
        if (! (bool) $actor->permiso_crear_solicitudes) {
            return false;
        }
        if (! $this->solicitudPerteneceAOrganizacionCliente($solicitud, $actor)) {
            return false;
        }

        if ((int) $solicitud->activo !== 1) {
            return false;
        }

        $est = mb_strtolower(trim((string) ($solicitud->estado ?? '')));
        if ($est === '') {
            return false;
        }

        return ! str_contains($est, 'complet') && ! str_contains($est, 'cancel');
    }

    private function clientePuedeGestionPropiaSiRegistrado(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);
        if ($rol === null) {
            return false;
        }
        if ($actor->id_cliente === null) {
            return false;
        }
        if (! in_array($rol, [UserRole::Cliente, UserRole::AdminCliente], true)) {
            return false;
        }
        if (! (bool) $actor->permiso_crear_solicitudes) {
            return false;
        }
        if (! $this->solicitudPerteneceAOrganizacionCliente($solicitud, $actor)) {
            return false;
        }

        return trim((string) $solicitud->estado) === 'Registrado';
    }

    /**
     * Cancelar mientras la solicitud siga activa y no esté cerrada (Completada/Cancelada).
     */
    private function clientePuedeCancelarSolicitud(Usuario $actor, Solicitud $solicitud): bool
    {
        $rol = UserRole::tryFrom((int) $actor->id_rol);
        if ($rol === null) {
            return false;
        }
        if ($actor->id_cliente === null) {
            return false;
        }
        if (! in_array($rol, [UserRole::Cliente, UserRole::AdminCliente], true)) {
            return false;
        }
        if (! (bool) $actor->permiso_crear_solicitudes) {
            return false;
        }
        if (! $this->solicitudPerteneceAOrganizacionCliente($solicitud, $actor)) {
            return false;
        }
        if ((int) $solicitud->activo !== 1) {
            return false;
        }

        $est = mb_strtolower(trim((string) $solicitud->estado));
        if ($est === '') {
            return false;
        }

        return ! str_contains($est, 'complet') && ! str_contains($est, 'cancel');
    }
}
