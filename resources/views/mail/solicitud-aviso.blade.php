<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $asunto }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #212529; line-height: 1.5;">
    <h2 style="color: #0d3a66;">Nueva notificación — Plataforma SJ Confiable</h2>
    <p><strong>Tipo:</strong> {{ $tipoEtiqueta }}</p>
    <p><strong>Cliente:</strong> {{ $clienteNombre }}</p>
    @if ($usuarioLinea)
        <p><strong>Usuario:</strong> {{ $usuarioLinea }}</p>
    @endif
    @if ($idSolicitud)
        <p><strong>Solicitud:</strong> #{{ $idSolicitud }}</p>
    @endif
    <p><strong>Mensaje:</strong></p>
    <p>{!! nl2br(e($mensaje)) !!}</p>
    @if ($urlPlataforma !== '')
        <p>Por favor, ingresa a la plataforma para más detalles:</p>
        <p><a href="{{ $urlPlataforma }}">{{ $urlPlataforma }}</a></p>
    @endif
</body>
</html>
