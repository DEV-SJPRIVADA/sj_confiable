@extends('layouts.app')

@section('title', 'Nuevo cliente')

@section('content')
    <div class="mb-3">
        <a href="{{ route('panel.consultor.clientes.index') }}" class="text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Volver al listado</a>
    </div>
    <div class="header-container-clientes mb-3">
        <h1 class="fw-light">Nuevo cliente</h1>
    </div>
    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('panel.consultor.clientes.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="nit">NIT</label>
                    <input type="number" name="nit" id="nit" class="form-control" value="{{ old('nit') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="razon_social">Razón social</label>
                    <input type="text" name="razon_social" id="razon_social" class="form-control" value="{{ old('razon_social') }}" required maxlength="255">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="tipo_cliente">Tipo cliente</label>
                    <input type="text" name="tipo_cliente" id="tipo_cliente" class="form-control" value="{{ old('tipo_cliente', 'Grupo') }}" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="direccion_cliente">Dirección</label>
                    <input type="text" name="direccion_cliente" id="direccion_cliente" class="form-control" value="{{ old('direccion_cliente') }}" maxlength="255">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="ciudad_cliente">Ciudad</label>
                    <input type="text" name="ciudad_cliente" id="ciudad_cliente" class="form-control" value="{{ old('ciudad_cliente') }}" maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="telefono_cliente">Teléfono</label>
                    <input type="text" name="telefono_cliente" id="telefono_cliente" class="form-control" value="{{ old('telefono_cliente') }}" maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="correo_cliente">Correo</label>
                    <input type="email" name="correo_cliente" id="correo_cliente" class="form-control" value="{{ old('correo_cliente') }}" maxlength="255">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="nombre">Nombre contacto</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="cargo">Cargo</label>
                    <input type="text" name="cargo" id="cargo" class="form-control" value="{{ old('cargo') }}" maxlength="100">
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>
@endsection
