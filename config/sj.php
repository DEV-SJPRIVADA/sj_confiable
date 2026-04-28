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

    /*
    | Búsqueda de archivos referenciados en documentos / documentos_respuesta (rutas legadas).
    | Copie la carpeta de uploads del servidor viejo y defina LEGACY_DOCUMENTS_ROOT apuntando a ella.
    */
    'document_roots' => array_values(array_unique(array_filter(array_merge(
        [env('LEGACY_DOCUMENTS_ROOT', '') !== '' ? (string) env('LEGACY_DOCUMENTS_ROOT') : null],
        [
            public_path(),
            public_path('uploads'),
            public_path('documentos'),
            storage_path('app/public/uploads'),
            storage_path('app/public/documentos'),
            storage_path('app/public'),
        ],
    )))),
];
