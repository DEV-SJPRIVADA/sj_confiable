@extends('layouts.app')

@php
    $perfilUpdateRoute = $perfilUpdateRoute ?? 'panel.consultor.perfil.update';
    $perfilTitle = $perfilTitle ?? 'Mi Perfil — Consultor';
    $nombrePantalla = trim(implode(' ', array_filter([
        $persona->nombre,
        $persona->paterno,
        trim((string) ($persona->materno ?? '')) !== '' ? trim((string) $persona->materno) : null,
    ])));
    if ($nombrePantalla === '') {
        $nombrePantalla = (string) $usuario->usuario;
    }
    $miembroDesde = $usuario->fecha_insert?->format('Y-m-d') ?? '—';
@endphp

@section('title', $perfilTitle)

@push('styles')
<style>
    /* Fondo de página: gris muy claro arriba → azul muy pálido abajo (legado) */
    .perfil-consultor-page {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 4.5rem);
        margin: -0.5rem 0 0 0;
        padding: 1.25rem 1rem 1.5rem;
        background: linear-gradient(180deg, #eceef1 0%, #e9edf3 35%, #dfe8f3 70%, #d8e4f2 100%);
    }
    /* Tarjeta: degradado azul casi imperceptible arriba → blanco abajo (gana a .card de Bootstrap) */
    .perfil-consultor-card {
        --bs-card-bg: transparent;
        max-width: 32rem;
        width: 100%;
        border-radius: 0.85rem;
        background: linear-gradient(180deg, #e8f2fa 0%, #f0f6fb 32%, #fafcfe 70%, #ffffff 100%) !important;
        box-shadow: 0 0.35rem 1.5rem rgba(13, 59, 102, 0.08), 0 0.08rem 0.25rem rgba(13, 59, 102, 0.04);
        overflow: hidden;
    }
    .perfil-consultor-card .card-body {
        background: transparent;
    }
    .perfil-card-top {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 0.25rem;
    }
    .perfil-avatar-wrap {
        position: relative;
        width: 9.5rem;
        height: 9.5rem;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 0.35rem;
        flex-shrink: 0;
    }
    .perfil-avatar-circle {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(145deg, #eef2f6 0%, #e2e8ef 100%);
        border: 1px solid #cfd8e3;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .perfil-avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .perfil-avatar-placeholder {
        color: #8a96a3;
        font-size: 0.8rem;
        line-height: 1.25;
        text-align: center;
        padding: 0 0.75rem;
        max-width: 7rem;
    }
    /* Cámara: esquina inferior derecha del círculo (como legado) */
    .perfil-avatar-cam {
        position: absolute;
        right: -0.15rem;
        bottom: -0.15rem;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0.15rem 0.5rem rgba(13, 59, 102, 0.25);
        z-index: 2;
    }
    .perfil-miembro {
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        color: #8b98a6;
    }
    .perfil-ico { color: #2c3e5c; min-width: 1.1rem; }
    .perfil-dato .label { font-weight: 600; color: #2a3440; }
    .perfil-dato .label::after { content: ':'; }
    .modal-actualizar-datos .modal-header {
        background: linear-gradient(90deg, #0d3b66 0%, #0d5a9e 100%);
    }
    .modal-actualizar-datos .form-control, .modal-actualizar-datos .form-select {
        border-color: #ced4da;
    }
</style>
@endpush

@section('content')
    <div class="perfil-consultor-page">
        <div class="perfil-consultor-card card border-0 mx-auto">
            <div class="card-body text-center p-4 p-md-5 pt-4">
            <div class="perfil-card-top" id="fotoPerfilApp">
                <div class="perfil-avatar-wrap">
                    <div class="perfil-avatar-circle" id="fotoPill" role="img" aria-label="Foto de perfil">
                        <img src="" alt="" class="d-none" id="fotoPreview" width="152" height="152">
                        <span class="perfil-avatar-placeholder" id="fotoLabel">Foto de perfil</span>
                    </div>
                    <input type="file" class="d-none" id="fotoInput" accept="image/*" aria-label="Elegir foto de perfil">
                    <button type="button" class="btn btn-primary perfil-avatar-cam" id="btnElegirFoto" title="Cambiar foto" aria-label="Cambiar foto de perfil">
                        <i class="fas fa-camera text-white" aria-hidden="true"></i>
                    </button>
                </div>
                <h1 class="h4 fw-bold text-primary mt-2 mb-1 px-2">{{ $nombrePantalla }}</h1>
                <p class="perfil-miembro text-uppercase mb-0 mb-md-1">Miembro desde {{ $miembroDesde }}</p>
            </div>
            <div class="row text-start small perfil-dato g-3 mt-3 mt-md-4 mb-4">
                <div class="col-6">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="fas fa-phone perfil-ico mt-1" aria-hidden="true"></i>
                        <div>
                            <div class="label">Teléfono</div>
                            <div>{{ $persona->telefono !== null && $persona->telefono !== '' ? $persona->telefono : '—' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="fas fa-envelope perfil-ico mt-1" aria-hidden="true"></i>
                        <div>
                            <div class="label">Correo</div>
                            <div class="text-break">{{ $persona->correo ?? '—' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="fas fa-mobile-screen-button perfil-ico mt-1" aria-hidden="true"></i>
                        <div>
                            <div class="label">Celular</div>
                            <div>{{ $persona->celular !== null && $persona->celular !== '' ? $persona->celular : '—' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="fas fa-location-dot perfil-ico mt-1" aria-hidden="true"></i>
                        <div>
                            <div class="label">Dirección</div>
                            <div>{{ $persona->direccion !== null && $persona->direccion !== '' ? $persona->direccion : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <button type="button" class="btn btn-primary text-uppercase" data-bs-toggle="modal" data-bs-target="#modalPerfilDatos" id="btnAbrirEditar">
                    <i class="far fa-edit me-2" aria-hidden="true"></i>Editar datos
                </button>
                <button type="button" class="btn btn-outline-secondary text-uppercase bg-white" id="btnRefrescarFoto">
                    <i class="fas fa-sync-alt me-2" aria-hidden="true"></i>Refrescar foto
                </button>
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-actualizar-datos" id="modalPerfilDatos" tabindex="-1" aria-labelledby="modalPerfilDatosLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white border-0">
                    <h2 class="modal-title fs-6 fw-bold" id="modalPerfilDatosLabel">Actualizar Datos Personales</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form method="post" action="{{ route($perfilUpdateRoute) }}">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="paterno">Primer apellido</label>
                                <input type="text" name="paterno" id="paterno" class="form-control @error('paterno') is-invalid @enderror" value="{{ old('paterno', $persona->paterno) }}" required maxlength="245" autocomplete="family-name">
                                @error('paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="materno">Segundo apellido</label>
                                <input type="text" name="materno" id="materno" class="form-control @error('materno') is-invalid @enderror" value="{{ old('materno', $persona->materno) }}" maxlength="245" autocomplete="additional-name">
                                @error('materno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="nombre">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $persona->nombre) }}" required maxlength="245" autocomplete="given-name">
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="telefono">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $persona->telefono) }}" maxlength="15" autocomplete="tel">
                                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="correo">Correo</label>
                                <input type="email" name="correo" id="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $persona->correo) }}" required maxlength="245" autocomplete="email">
                                @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="celular">Celular</label>
                                <input type="text" name="celular" id="celular" class="form-control @error('celular') is-invalid @enderror" value="{{ old('celular', $persona->celular) }}" required maxlength="15" autocomplete="tel-national">
                                @error('celular')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="direccion">Dirección</label>
                                <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $persona->direccion) }}" maxlength="100" autocomplete="street-address">
                                @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-start">
                        <button type="submit" class="btn btn-primary text-uppercase">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    var shouldOpen = @json((bool) session('open_perfil_modal') || $errors->any());
    var input = document.getElementById('fotoInput');
    var btnCam = document.getElementById('btnElegirFoto');
    var btnRef = document.getElementById('btnRefrescarFoto');
    var img = document.getElementById('fotoPreview');
    var label = document.getElementById('fotoLabel');
    var modalEl = document.getElementById('modalPerfilDatos');

    function showLabel(show) {
        if (label) { label.classList.toggle('d-none', !show); }
    }
    function clearPreview() {
        if (input) { input.value = ''; }
        if (img) {
            img.src = '';
            img.classList.add('d-none');
        }
        showLabel(true);
    }
    if (btnCam && input) {
        btnCam.addEventListener('click', function () { input.click(); });
    }
    if (input && img) {
        input.addEventListener('change', function () {
            var f = this.files && this.files[0];
            if (!f || !f.type || f.type.indexOf('image/') !== 0) { return; }
            var u = URL.createObjectURL(f);
            img.onload = function () { URL.revokeObjectURL(u); };
            img.src = u;
            img.classList.remove('d-none');
            showLabel(false);
        });
    }
    if (btnRef) { btnRef.addEventListener('click', clearPreview); }
    if (shouldOpen && modalEl && window.bootstrap) {
        document.addEventListener('DOMContentLoaded', function () {
            var m = new bootstrap.Modal(modalEl);
            m.show();
        });
    }
})();
</script>
@endpush
