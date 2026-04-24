@php
    $u = auth()->user();
@endphp
<nav class="navbar navbar-expand-lg navbar-dark fixed-top large-navbar">
    <div class="container-fluid position-relative">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('panel.cliente.solicitudes.index') }}">
            <img src="{{ asset('images/logo-sj-confiable.png') }}" alt="SJ Confiable" class="navbar-logo me-2" style="height:2.7rem;max-height:3rem;width:12rem;object-fit:contain;">
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCliente" aria-controls="navbarCliente" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCliente">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Inicio</a>
                </li>
                <li class="nav-item"><span class="nav-link text-white-50" title="Pendiente de migración">Nueva Solicitud</span></li>
                <li class="nav-item"><span class="nav-link text-white-50" title="Pendiente de migración">Importar</span></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('panel.cliente.solicitudes.*') ? 'active' : '' }}" href="{{ route('panel.cliente.solicitudes.index') }}">Solicitudes</a>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0 ms-lg-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2"></i>{{ $u->usuario }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown">
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
