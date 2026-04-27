@php
    $roleId = auth()->check() ? (int) auth()->user()->id_rol : 0;
    $plPath = public_path('css/legacy/plantilla.css');
    $assetsV = (string) (is_string($plPath) && is_file($plPath) ? filemtime($plPath) : time());
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/legacy/plantilla.css') }}?v={{ $assetsV }}">
    <link rel="stylesheet" href="{{ asset('css/legacy/tablas-optimizadas.css') }}?v={{ $assetsV }}">
    <link rel="stylesheet" href="{{ asset('css/legacy/panel-tables-laravel.css') }}?v={{ $assetsV }}">
    @stack('styles')
</head>
<body>
@auth
    @if (in_array($roleId, [2, 3], true))
        @include('layouts.partials.navbar-consultor')
    @elseif (in_array($roleId, [1, 4, 5], true))
        @include('layouts.partials.navbar-cliente')
    @elseif ($roleId === 6)
        @include('layouts.partials.navbar-proveedor')
    @endif
@endauth
<div id="app-page">
<main class="container-fluid @auth py-3 @else py-0 @endauth">
    @if (session('error'))
        <div class="alert alert-warning d-flex align-items-center" role="alert"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}</div>
    @endif
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    @yield('content')
</main>
@auth
    @if (in_array($roleId, [2, 3], true))
        <footer class="text-center text-muted small py-3 border-top border-secondary border-opacity-25 bg-body-secondary bg-opacity-25 mt-3">
            <div class="container-fluid">SJ Seguridad Privada LTDA &copy; {{ date('Y') }}. Todos los derechos reservados.</div>
        </footer>
    @endif
@endauth
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
