<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

use App\Mail\SolicitudAvisoMail;
use App\Models\Proveedor;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envío de correo alineado con {@see SolicitudNotificacionService} (mismos destinatarios por evento).
 */
final class SolicitudCorreoNotificacionService
{
    private const ROLES_CONSULTOR_SJ = [2, 3];

    public function nuevaSolicitudParaConsultoresSj(
        string $tipo,
        string $razonCliente,
        int $idSolicitud,
        string $mensaje,
        ?string $usuarioLinea = null,
    ): void {
        $asunto = 'Nueva Solicitud de confiabilidad - '.$razonCliente;
        $this->enviarAConsultoresSj($tipo, $razonCliente, $idSolicitud, $mensaje, $asunto, $usuarioLinea);
    }

    public function respuestaProveedorParaConsultoresSj(
        string $tipo,
        string $razonCliente,
        int $idSolicitud,
        string $mensaje,
    ): void {
        $asunto = 'Respuesta proveedor Solicitud #'.$idSolicitud;
        $this->enviarAConsultoresSj($tipo, $razonCliente, $idSolicitud, $mensaje, $asunto);
    }

    public function mensajeParaOrganizacionCliente(
        int $idCliente,
        string $tipo,
        string $razonCliente,
        int $idSolicitud,
        string $mensaje,
    ): void {
        if (! $this->correosHabilitados()) {
            return;
        }

        $emails = $this->emailsUsuariosCliente($idCliente);
        $asunto = 'Actualización solicitud #'.$idSolicitud.' — SJ Confiable';
        $url = $this->urlPlataforma();

        $this->enviarVarios($emails, new SolicitudAvisoMail(
            $asunto,
            $tipo,
            $razonCliente,
            $mensaje,
            $idSolicitud,
            null,
            $url,
        ));
    }

    public function asignacionParaProveedor(
        int $idProveedor,
        string $tipo,
        string $nombreProveedor,
        int $idSolicitud,
        string $mensaje,
    ): void {
        if (! $this->correosHabilitados()) {
            return;
        }

        $emails = $this->emailsProveedor($idProveedor);
        $asunto = 'Asignación de solicitud #'.$idSolicitud;
        $url = $this->urlPlataforma();

        $this->enviarVarios($emails, new SolicitudAvisoMail(
            $asunto,
            $tipo,
            $nombreProveedor,
            $mensaje,
            $idSolicitud,
            null,
            $url,
        ));
    }

    /**
     * Línea "usuario - nombre" del creador de la solicitud (paridad legado).
     */
    public function lineaCreadorSolicitud(Solicitud $solicitud): ?string
    {
        $solicitud->loadMissing(['creador.persona']);
        $creador = $solicitud->creador;
        if ($creador === null) {
            return null;
        }

        $login = trim((string) ($creador->usuario ?? ''));
        $p = $creador->persona;
        $nombre = '';
        if ($p !== null) {
            $nombre = trim(implode(' ', array_filter([
                trim((string) ($p->nombre ?? '')),
                trim((string) ($p->paterno ?? '')),
                trim((string) ($p->materno ?? '')),
            ])));
        }

        if ($login === '' && $nombre === '') {
            return null;
        }

        return $nombre !== '' ? trim($login.' - '.$nombre) : $login;
    }

    private function enviarAConsultoresSj(
        string $tipo,
        string $razonCliente,
        int $idSolicitud,
        string $mensaje,
        string $asunto,
        ?string $usuarioLinea = null,
    ): void {
        if (! $this->correosHabilitados()) {
            return;
        }

        $emails = $this->emailsConsultoresSj();
        $url = $this->urlPlataforma();

        $this->enviarVarios($emails, new SolicitudAvisoMail(
            $asunto,
            $tipo,
            $razonCliente,
            $mensaje,
            $idSolicitud,
            $usuarioLinea,
            $url,
        ));
    }

    /**
     * @return list<string>
     */
    private function emailsConsultoresSj(): array
    {
        return $this->extraerCorreos(
            Usuario::query()
                ->whereIn('id_rol', self::ROLES_CONSULTOR_SJ)
                ->where('activo', 1)
                ->with('persona:id_persona,correo')
                ->get(),
        );
    }

    /**
     * @return list<string>
     */
    private function emailsUsuariosCliente(int $idCliente): array
    {
        return $this->extraerCorreos(
            Usuario::query()
                ->where('id_cliente', $idCliente)
                ->where('activo', 1)
                ->with('persona:id_persona,correo')
                ->get(),
        );
    }

    /**
     * @return list<string>
     */
    private function emailsProveedor(int $idProveedor): array
    {
        $out = [];
        $proveedor = Proveedor::query()->whereKey($idProveedor)->first();
        if ($proveedor !== null) {
            $correo = trim((string) ($proveedor->correo_proveedor ?? ''));
            if ($correo !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $out[] = strtolower($correo);
            }
        }

        $usuarios = $this->extraerCorreos(
            Usuario::query()
                ->where('id_proveedor', $idProveedor)
                ->where('activo', 1)
                ->with('persona:id_persona,correo')
                ->get(),
        );

        return array_values(array_unique(array_merge($out, $usuarios)));
    }

    /**
     * @param  Collection<int, Usuario>  $usuarios
     * @return list<string>
     */
    private function extraerCorreos(Collection $usuarios): array
    {
        $emails = [];
        foreach ($usuarios as $u) {
            $correo = trim((string) ($u->persona?->correo ?? ''));
            if ($correo !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $emails[] = strtolower($correo);
            }
        }

        return array_values(array_unique($emails));
    }

  /**
   * @param  list<string>  $emails
   */
    private function enviarVarios(array $emails, SolicitudAvisoMail $mailable): void
    {
        if ($emails === []) {
            return;
        }

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(clone $mailable);
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar aviso por correo.', [
                    'email' => $email,
                    'asunto' => $mailable->asunto,
                    'solicitud_id' => $mailable->idSolicitud,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function correosHabilitados(): bool
    {
        return (bool) config('notifications.email_enabled', true);
    }

    private function urlPlataforma(): string
    {
        return rtrim((string) config('app.url', ''), '/');
    }
}
