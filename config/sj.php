<?php

declare(strict_types=1);

return [

    /*
    | Enlace a WhatsApp (p. ej. https://wa.me/573001234567). Vacío: no se muestra el botón flotante.
    */
    'whatsapp_url' => env('SJ_WHATSAPP_URL', ''),

    /*
    | Recuperar contraseña (legado o página externa). Vacío: el enlace apunta a "#".
    */
    'forgot_password_url' => env('SJ_FORGOT_PASSWORD_URL', ''),

    'social' => [
        'facebook' => env('SJ_SOCIAL_FACEBOOK', ''),
        'linkedin' => env('SJ_SOCIAL_LINKEDIN', ''),
        'instagram' => env('SJ_SOCIAL_INSTAGRAM', ''),
    ],
];
