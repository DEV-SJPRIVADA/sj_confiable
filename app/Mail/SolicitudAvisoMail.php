<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class SolicitudAvisoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $asunto,
        public readonly string $tipoEtiqueta,
        public readonly string $clienteNombre,
        public readonly string $mensaje,
        public readonly ?int $idSolicitud = null,
        public readonly ?string $usuarioLinea = null,
        public readonly string $urlPlataforma = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.solicitud-aviso',
        );
    }
}
