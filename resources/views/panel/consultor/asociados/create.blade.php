@extends('layouts.app')

@section('title', 'Nuevo asociado')

@section('content')
    <div class="mb-3">
        <a href="{{ route('panel.consultor.asociados.index') }}" class="text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Volver al listado</a>
    </div>
    <div class="header-container-proveedores mb-3">
        <h1 class="fw-light">Nuevo asociado de negocios</h1>
    </div>
    <div class="card border-0 shadow-sm" style="max-width: 42rem;">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('panel.consultor.asociados.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="NIT_proveedor">NIT</label>
                        <input type="number" name="NIT_proveedor" id="NIT_proveedor" class="form-control" value="{{ old('NIT_proveedor') }}" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="razon_social_proveedor">Razón social</label>
                        <input type="text" name="razon_social_proveedor" id="razon_social_proveedor" class="form-control" value="{{ old('razon_social_proveedor') }}" required maxlength="50">
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label" for="nombre_comercial">Nombre comercial</label>
                        <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control" value="{{ old('nombre_comercial') }}" required maxlength="50">
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label" for="correo_proveedor">Correo</label>
                        <input type="email" name="correo_proveedor" id="correo_proveedor" class="form-control" value="{{ old('correo_proveedor') }}" required maxlength="50">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="telefono_proveedor">Teléfono fijo</label>
                        <input type="text" name="telefono_proveedor" id="telefono_proveedor" class="form-control" value="{{ old('telefono_proveedor') }}" maxlength="50">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="celular_proveedor">Celular</label>
                        <input type="text" name="celular_proveedor" id="celular_proveedor" class="form-control" value="{{ old('celular_proveedor') }}" required maxlength="50">
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label" for="direccion_proveedor">Dirección</label>
                        <input type="text" name="direccion_proveedor" id="direccion_proveedor" class="form-control" value="{{ old('direccion_proveedor') }}" required maxlength="50">
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label" for="ciudad_proveedor">Ciudad</label>
                        <input type="text" name="ciudad_proveedor" id="ciudad_proveedor" class="form-control" value="{{ old('ciudad_proveedor') }}" required maxlength="50">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="nombre_contacto_proveedor">Contacto</label>
                        <input type="text" name="nombre_contacto_proveedor" id="nombre_contacto_proveedor" class="form-control" value="{{ old('nombre_contacto_proveedor') }}" required maxlength="50">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="cargo_contacto_proveedor">Cargo</label>
                        <input type="text" name="cargo_contacto_proveedor" id="cargo_contacto_proveedor" class="form-control" value="{{ old('cargo_contacto_proveedor') }}" required maxlength="50">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Guardar</button>
            </form>
        </div>
    </div>
@endsection
