@php
    $proveedorRolId = \App\Domain\Enums\UserRole::Proveedor->value;
@endphp
@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
    <div class="mb-3">
        <a href="{{ route('panel.consultor.usuarios.index') }}" class="text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Volver al listado</a>
    </div>
    <div class="mb-3">
        <h1 class="fw-light" style="font-size:1.75rem;">Nuevo usuario</h1>
    </div>
    <div class="card border-0 shadow-sm" style="max-width: 48rem;">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('panel.consultor.usuarios.store') }}" id="formUsuario">
                @csrf
                <div class="row g-2 small">
                    <div class="col-md-4 mb-2">
                        <label class="form-label" for="paterno">Apellido paterno *</label>
                        <input type="text" name="paterno" id="paterno" class="form-control" value="{{ old('paterno') }}" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label" for="materno">Apellido materno</label>
                        <input type="text" name="materno" id="materno" class="form-control" value="{{ old('materno') }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label" for="nombre">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="id_rol">Rol *</label>
                        <select name="id_rol" id="id_rol" class="form-select" required>
                            <option value="">—</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->id_rol }}" @selected((int) old('id_rol') === (int) $r->id_rol)>
                                    {{ $r->nombre }} ({{ $r->id_rol }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2" id="wrap-cliente">
                        <label class="form-label" for="id_cliente">Cliente *</label>
                        <select name="id_cliente" id="id_cliente" class="form-select">
                            <option value="">—</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id_cliente }}" @selected((int) old('id_cliente') === (int) $c->id_cliente)>
                                    {{ $c->razon_social }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2 d-none" id="wrap-proveedor">
                        <label class="form-label" for="id_proveedor">Asociado *</label>
                        <select name="id_proveedor" id="id_proveedor" class="form-select">
                            <option value="">—</option>
                            @foreach ($proveedores as $p)
                                <option value="{{ $p->id_proveedor }}" @selected((int) old('id_proveedor') === (int) $p->id_proveedor)>
                                    {{ $p->razon_social_proveedor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="usuario">Usuario (login) *</label>
                        <input type="text" name="usuario" id="usuario" class="form-control" value="{{ old('usuario') }}" required maxlength="245" autocomplete="off">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="password">Contraseña *</label>
                        <input type="password" name="password" id="password" class="form-control" required minlength="6" maxlength="500" autocomplete="new-password">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label" for="ciudad">Ciudad *</label>
                        <input type="text" name="ciudad" id="ciudad" class="form-control" value="{{ old('ciudad', 'Cali') }}" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label" for="telefono">Teléfono *</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}" required maxlength="15">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label" for="celular">Celular *</label>
                        <input type="text" name="celular" id="celular" class="form-control" value="{{ old('celular') }}" required maxlength="15">
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label" for="correo">Correo (persona) *</label>
                        <input type="email" name="correo" id="correo" class="form-control" value="{{ old('correo') }}" required>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label" for="direccion">Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion') }}" maxlength="100">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="identificacion">Identificación</label>
                        <input type="text" name="identificacion" id="identificacion" class="form-control" value="{{ old('identificacion') }}" maxlength="50">
                    </div>
                </div>
                <p class="text-muted small mt-2">Permisos (cliente)</p>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="permiso_ver_documentos" id="pv" value="1" @checked(old('permiso_ver_documentos'))>
                    <label class="form-check-label" for="pv">Ver documentos</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="permiso_subir_documentos" id="ps" value="1" @checked(old('permiso_subir_documentos'))>
                    <label class="form-check-label" for="ps">Subir documentos</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="permiso_crear_solicitudes" id="pc" value="1" @checked(old('permiso_crear_solicitudes'))>
                    <label class="form-check-label" for="pc">Crear solicitudes</label>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script>
    (function(){
        const prov = {{ (int) $proveedorRolId }};
        const sel = document.getElementById('id_rol');
        const wC = document.getElementById('wrap-cliente');
        const wP = document.getElementById('wrap-proveedor');
        const c = document.getElementById('id_cliente');
        const p = document.getElementById('id_proveedor');
        function t(){
            const v = parseInt(sel.value, 10) || 0;
            if (v === prov) {
                wC.classList.add('d-none');
                c.removeAttribute('required');
                wP.classList.remove('d-none');
                p.setAttribute('required','required');
            } else {
                wP.classList.add('d-none');
                p.removeAttribute('required');
                wC.classList.remove('d-none');
                c.setAttribute('required','required');
            }
        }
        sel.addEventListener('change', t);
        t();
    })();
    </script>
    @endpush
@endsection
