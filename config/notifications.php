<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Correos de avisos operativos (solicitudes)
    |--------------------------------------------------------------------------
    |
    | Desactivar sin cambiar MAIL_MAILER (útil en local). Requiere SMTP configurado
    | (p. ej. Outlook: MAIL_HOST=smtp-mail.outlook.com, MAIL_PORT=587, MAIL_SCHEME=tls).
    |
    */

    'email_enabled' => env('MAIL_NOTIFICATIONS_ENABLED', true),

];
