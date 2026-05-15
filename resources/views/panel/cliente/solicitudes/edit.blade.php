@extends('layouts.app')

@php
    $picklistAsset = max(
        is_file(public_path('css/legacy/picklist-checkbox.css')) ? filemtime(public_path('css/legacy/picklist-checkbox.css')) : 0,
        is_file(public_path('js/solicitudes/picklist-checkbox.js')) ? filemtime(public_path('js/solicitudes/picklist-checkbox.js')) : 0,
    );
    $selServicios = old('servicio_ids', $servicioIdsSeleccionados ?? []);
    $tipoOpts = [
        'CC' => 'Cédula de Ciudadanía',
        'CE' => 'Cédula de Extranjería',
        'TI' => 'Tarjeta de Identidad',
        'PA' => 'Pasaporte',
        'NIT' => 'NIT',
        'PPT' => 'PPT',
        'PEP' => 'PEP',
    ];
@endphp

@section('title', 'Editar Solicitud #'.$solicitud->id)

@push('styles')
<style>
    .cli-edit-solicitud-root {
        max-width: 1100px;
        margin-inline: auto;
        padding-inline: clamp(0.75rem, 2vw, 1.25rem);
        padding-bottom: 2rem;
    }
    .cli-edit-form-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.35rem rgba(0, 0, 0, 0.08);
    }
    .cli-edit-form-card .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #212529;
    }
    .cli-edit-form-card .form-label .req { color: #c0392b; }
    .cli-edit-title {
        font-size: clamp(1.25rem, 2.5vw, 1.5rem);
        font-weight: 700;
        letter-spacing: 0.015em;
    }
</style>
@php
    $picklistServiciosItems = $servicios->map(static fn ($s) => [
        'value' => $s->id_servicio,
        'label' => $s->nombre,
    ])->all();
@endphp
<link rel="stylesheet" href="{{ asset('css/legacy/picklist-checkbox.css') }}?v={{ $picklistAsset }}">
@endpush

@section('content')
<div class="cli-edit-solicitud-root pt-2">
    <p class="mb-3 text-start">
        <a href="{{ route('panel.cliente.solicitudes.show', $solicitud) }}" class="text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Volver al detalle</a>
    </p>
    @if ($errors->any())
        <div class="alert alert-danger small mb-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="cli-edit-form-card px-3 px-md-4 py-4">
        <h1 class="cli-edit-title text-center fw-bold mb-2 text-primary">Editar Solicitud #{{ $solicitud->id }}</h1>
        <p class="text-muted small text-center mb-4">Actualice los datos mientras la solicitud esté activa y no esté completada (paridad sistema anterior).</p>

        <form method="post" action="{{ route('panel.cliente.solicitudes.update', $solicitud) }}" id="formEditSolicitudCliente" novalidate>
            @csrf
            @method('PUT')

            {{-- Empresa / NIT (solo lectura, vienen del cliente) --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label mb-1" for="empresa_solicitante_ro">Empresa Solicitante</label>
                    <input type="text" id="empresa_solicitante_ro" class="form-control-plaintext rounded border px-2 py-2 bg-light" readonly value="{{ e($solicitud->empresa_solicitante ?? '') }}" tabindex="-1">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label mb-1" for="nit_ro">NIT Empresa Solicitante</label>
                    <input type="text" id="nit_ro" class="form-control-plaintext rounded border px-2 py-2 bg-light" readonly value="{{ e($solicitud->nit_empresa_solicitante ?? '') }}" tabindex="-1">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="cliente_final">Cliente Final <span class="req">*</span></label>
                    <input type="text" name="cliente_final" id="cliente_final" class="form-control" required maxlength="100" value="{{ old('cliente_final', $solicitud->cliente_final ?? '') }}" autocomplete="organization">
                </div>
                <div class="col-12 col-md-6"></div>
            </div>

            <div class="row g-3 mb-3 align-items-stretch">
                <div class="col-12 col-md-6 d-flex flex-column">
                    <label class="form-label" for="picklist-servicios-edit-trigger">Servicios (1 a 5) <span class="req">*</span></label>
                    @include('panel.partials._picklist-checkbox', [
                        'id' => 'picklist-servicios-edit',
                        'name' => 'servicio_ids',
                        'multiple' => true,
                        'max' => 5,
                        'placeholder' => 'Seleccione servicio',
                        'selected' => $selServicios,
                        'items' => $picklistServiciosItems,
                        'role' => 'servicios-multiple',
                    ])
                    <small class="text-muted mt-1">Haga clic para elegir. Máx. 5.</small>
                    <small id="serviciosEditError" class="text-danger d-none mt-1" role="status">Seleccione entre 1 y 5 servicios.</small>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="ciudad_prestacion_servicio">Ciudad Prestación del Servicio <span class="req">*</span></label>
                    <select name="ciudad_prestacion_servicio" id="ciudad_prestacion_servicio" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c }}" @selected(old('ciudad_prestacion_servicio', $solicitud->ciudad_prestacion_servicio) === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="ciudad_solicitud_servicio">Ciudad Solicitud del Servicio <span class="req">*</span></label>
                    <select name="ciudad_solicitud_servicio" id="ciudad_solicitud_servicio" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c }}" @selected(old('ciudad_solicitud_servicio', $solicitud->ciudad_solicitud_servicio) === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="nombres">Nombres <span class="req">*</span></label>
                    <input type="text" name="nombres" id="nombres" class="form-control" required maxlength="30" value="{{ old('nombres', $solicitud->nombres) }}" autocomplete="given-name">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="apellidos">Apellidos <span class="req">*</span></label>
                    <input type="text" name="apellidos" id="apellidos" class="form-control" required maxlength="50" value="{{ old('apellidos', $solicitud->apellidos) }}" autocomplete="family-name">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="tipo_identificacion">Tipo de Identificación <span class="req">*</span></label>
                    <select name="tipo_identificacion" id="tipo_identificacion" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($tipoOpts as $k => $lab)
                            <option value="{{ $k }}" @selected(old('tipo_identificacion', $solicitud->tipo_identificacion) === $k)>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="numero_documento">Número de Documento <span class="req">*</span></label>
                    <input type="text" name="numero_documento" id="numero_documento" class="form-control" required maxlength="15" value="{{ old('numero_documento', $solicitud->numero_documento) }}" inputmode="text" autocomplete="off">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="fecha_expedicion">Fecha de Expedición</label>
                    <input type="date" name="fecha_expedicion" id="fecha_expedicion" class="form-control" value="{{ old('fecha_expedicion', $solicitud->fecha_expedicion?->format('Y-m-d')) }}" placeholder="dd/mm/aaaa">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="lugar_expedicion">Lugar de Expedición</label>
                    <select name="lugar_expedicion" id="lugar_expedicion" class="form-select">
                        <option value="">Seleccione</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c }}" @selected(old('lugar_expedicion', $solicitud->lugar_expedicion) === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="telefono_fijo">Teléfono Fijo</label>
                    <input type="text" name="telefono_fijo" id="telefono_fijo" class="form-control" maxlength="10" value="{{ old('telefono_fijo', $solicitud->telefono_fijo) }}" inputmode="numeric">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="celular">Celular <span class="req">*</span></label>
                    <input type="text" name="celular" id="celular" class="form-control" required maxlength="30" value="{{ old('celular', $solicitud->celular) }}" inputmode="text" autocomplete="tel">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="ciudad_residencia_evaluado">Ciudad de Residencia del Evaluado <span class="req">*</span></label>
                    <select name="ciudad_residencia_evaluado" id="ciudad_residencia_evaluado" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c }}" @selected(old('ciudad_residencia_evaluado', $solicitud->ciudad_residencia_evaluado) === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="direccion_residencia">Dirección de Residencia <span class="req">*</span></label>
                <input type="text" name="direccion_residencia" id="direccion_residencia" class="form-control" required maxlength="50" value="{{ old('direccion_residencia', $solicitud->direccion_residencia) }}" autocomplete="street-address">
            </div>

            <div class="mb-3">
                <label class="form-label" for="cargo_candidato">Cargo Candidato <span class="req">*</span></label>
                <input type="text" name="cargo_candidato" id="cargo_candidato" class="form-control" required maxlength="100" value="{{ old('cargo_candidato', $solicitud->cargo_candidato) }}">
            </div>

            <div class="mb-4">
                <label class="form-label" for="comentarios">Comentarios</label>
                <textarea name="comentarios" id="comentarios" class="form-control" rows="4" maxlength="2000" placeholder="">{{ old('comentarios', $solicitud->comentarios) }}</textarea>
            </div>

            @if ($errors->has('servicio_ids'))
                <p class="text-danger small text-center fw-medium mb-3" id="msgServiciosEdit" role="status">
                    {{ $errors->first('servicio_ids') }}
                </p>
            @endif

            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center align-items-center py-3">
                <button type="submit" class="btn btn-primary text-uppercase fw-semibold px-5 py-2">Guardar cambios</button>
                <a href="{{ route('panel.cliente.solicitudes.show', $solicitud) }}" class="btn btn-secondary text-uppercase fw-semibold px-5 py-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/solicitudes/picklist-checkbox.js') }}?v={{ $picklistAsset }}"></script>
@endpush
