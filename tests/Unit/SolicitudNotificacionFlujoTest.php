<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Mail\SolicitudAvisoMail;
use App\Services\Solicitud\SolicitudCorreoNotificacionService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SolicitudNotificacionFlujoTest extends TestCase
{
    public function test_correo_deshabilitado_no_envia(): void
    {
        Mail::fake();
        config(['notifications.email_enabled' => false]);

        $svc = new SolicitudCorreoNotificacionService;
        $svc->mensajeParaOrganizacionCliente(1, 'tipo', 'Cliente', 1, 'Hola');

        Mail::assertNothingSent();
    }

    public function test_mailable_expone_datos_de_plantilla(): void
    {
        $mail = new SolicitudAvisoMail(
            'Asunto test',
            'confiabilidad',
            'SJ Seguridad',
            'Cuerpo del mensaje',
            42,
            'creador - Nombre',
            'http://localhost',
        );

        $this->assertSame('Asunto test', $mail->asunto);
        $this->assertSame(42, $mail->idSolicitud);
    }
}
