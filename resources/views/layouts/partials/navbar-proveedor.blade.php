@php
    $u = auth()->user();
    $proveedorNotificacionBadge = (int) ($proveedorNotificacionBadge ?? 0);
    $headerLogoRel = 'images/Logo Sj Confiable-02.png';
    $headerLogoPath = public_path($headerLogoRel);
    $logoNavbar = asset($headerLogoRel);
    $logoNavbar .= is_file($headerLogoPath) ? '?v='.filemtime($headerLogoPath) : '';
@endphp
<nav class="navbar navbar-expand-lg navbar-dark fixed-top large-navbar">
    <div class="container-fluid position-relative">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('panel.proveedor.inicio') }}">
            <img src="{{ $logoNavbar }}" alt="SJ Confiable" class="navbar-logo me-2" style="height:2.7rem;max-height:3rem;width:12rem;object-fit:contain;">
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarProv" aria-controls="navbarProv" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarProv">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 justify-content-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('panel.proveedor.inicio') ? 'active' : '' }}" href="{{ route('panel.proveedor.inicio') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('panel.proveedor.solicitudes.*') ? 'active' : '' }}" href="{{ route('panel.proveedor.solicitudes.index') }}">Solicitudes</a>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0 ms-lg-auto align-items-lg-center">
                <li class="nav-item me-2">
                    <a
                        class="nav-link py-0 position-relative"
                        href="#"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNotificacionesProveedor"
                        title="Notificaciones"
                    >
                        <i class="fas fa-bell fa-lg"></i>
                        @if ($proveedorNotificacionBadge > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;min-width:1.1rem;">{{ $proveedorNotificacionBadge > 99 ? '99+' : $proveedorNotificacionBadge }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2"></i>{{ $u->usuario }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalNotificacionesProveedor">
                                <i class="fas fa-bell me-2 text-warning"></i>Notificaciones
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('panel.proveedor.perfil.show') }}">
                                <i class="fas fa-id-badge me-2 text-info"></i>Mi Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-white border-opacity-25"></li>
                        <li>
                            <form method="post" action="{{ route('logout') }}" class="d-inline w-100">
                                @csrf
                                <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start"><i class="fas fa-sign-out-alt me-2 text-danger"></i>Salir</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
@include('layouts.partials.modal-notificaciones-proveedor')
