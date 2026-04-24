@php
    $u = auth()->user();
    $r = 'panel.consultor.';
    $inicio = request()->routeIs($r.'inicio');
    $usu = request()->routeIs($r.'usuarios.*');
    $cli = request()->routeIs($r.'clientes.*');
    $aso = request()->routeIs($r.'asociados.*');
    $inf = request()->routeIs($r.'informes.*');
    $solConf = request()->routeIs($r.'solicitudes.index') || request()->routeIs($r.'solicitudes.show') || request()->routeIs($r.'solicitudes.asignar');
    $solUsu = request()->routeIs($r.'solicitudes-usuarios.*');
    $menuSolicitudes = $solConf || $solUsu;
@endphp
<nav class="navbar navbar-expand-lg navbar-dark fixed-top large-navbar">
    <div class="container-fluid position-relative">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('panel.consultor.inicio') }}">
            <img src="{{ asset('images/logo-sj-confiable.png') }}" alt="SJ Confiable" class="navbar-logo me-2" width="192" height="48" style="height:2.7rem;max-height:3rem;width:12rem;object-fit:contain;">
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarConsultor" aria-controls="navbarConsultor" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarConsultor">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 justify-content-center">
                <li class="nav-item">
                    <a class="nav-link {{ $inicio ? 'active' : '' }}" href="{{ route('panel.consultor.inicio') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $usu ? 'active' : '' }}" href="{{ route('panel.consultor.usuarios.index') }}">Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $cli ? 'active' : '' }}" href="{{ route('panel.consultor.clientes.index') }}">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $aso ? 'active' : '' }}" href="{{ route('panel.consultor.asociados.index') }}">Asociados de negocios</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $menuSolicitudes ? 'active' : '' }}" href="#" id="solicitudesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Solicitudes
                    </a>
                    <ul class="dropdown-menu custom-dropdown animate__animated animate__fadeIn" aria-labelledby="solicitudesDropdown">
                        <li>
                            <a class="dropdown-item {{ $solConf && ! $solUsu ? 'fw-semibold' : '' }}" href="{{ route('panel.consultor.solicitudes.index') }}"><i class="fas fa-check-double me-2 text-info"></i>Confiabilidad</a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ $solUsu ? 'fw-semibold' : '' }}" href="{{ route('panel.consultor.solicitudes-usuarios.index') }}"><i class="fas fa-user-friends me-2 text-success"></i>Usuarios</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $inf ? 'active' : '' }}" href="{{ route('panel.consultor.informes.index') }}">Informes</a>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0 ms-lg-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2"></i>{{ $u->usuario }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown" aria-labelledby="perfilDropdown">
                        <li><span class="dropdown-item text-white-50"><i class="fas fa-id-badge me-2 text-info"></i>Mi Perfil (próximamente)</span></li>
                        <li><span class="dropdown-item text-white-50"><i class="fas fa-bell me-2 text-warning"></i>Notificaciones (próximamente)</span></li>
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
