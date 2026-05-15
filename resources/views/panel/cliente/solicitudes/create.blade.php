@extends('layouts.app')

@php
    $picklistAsset = max(
        is_file(public_path('css/legacy/picklist-checkbox.css')) ? filemtime(public_path('css/legacy/picklist-checkbox.css')) : 0,
        is_file(public_path('js/solicitudes/picklist-checkbox.js')) ? filemtime(public_path('js/solicitudes/picklist-checkbox.js')) : 0,
    );
@endphp

@section('title', 'Agregar solicitud — Cliente')

@push('styles')
<style>
    /* Ocupa casi todo el ancho útil como el legado (compensa gutter de container-fluid) */
    .panel-solicitud-create {
        width: 100%;
        max-width: none;
        margin: -0.75rem -1.25rem 0 -1.25rem;
        padding: 0 clamp(0.5rem, 2vw, 1.25rem) 1.5rem;
    }
    @media (max-width: 575.98px) {
        .panel-solicitud-create {
            margin: -0.75rem -0.75rem 0 -0.75rem;
        }
    }
    .panel-solicitud-create .titulo-principal { font-size: 1.65rem; letter-spacing: 0.02em; }
    .panel-solicitud-create .form-label { font-size: 0.875rem; font-weight: 500; color: #212529; }
    .panel-solicitud-create .form-label .req { color: #c0392b; }
    .panel-solicitud-create .form-hint { font-size: 0.75rem; color: #0d6efd; line-height: 1.35; }
    .panel-solicitud-create .card-section {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.35rem;
        box-shadow: 0 0.12rem 0.4rem rgba(0, 0, 0, 0.06);
    }
</style>
<link rel="stylesheet" href="{{ asset('css/legacy/picklist-checkbox.css') }}?v={{ $picklistAsset }}">
@endpush

@section('content')
<div class="panel-solicitud-create">
    <p class="mb-3 text-center text-md-start">
        <a href="{{ route('panel.cliente.solicitudes.index') }}" class="text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Volver a solicitudes</a>
    </p>
    <h1 class="titulo-principal text-center fw-bold mb-1 text-body">Agregar Solicitud</h1>
    <p class="text-muted small mb-4 text-center">Complete los campos. Los marcados con <span class="req">*</span> son obligatorios.</p>

    @if ($errors->any())
        <div class="alert alert-danger small mb-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('panel.cliente.solicitudes.store') }}" id="formNuevaSolicitudCliente" novalidate>
        @csrf
        <div class="card border-0 shadow-sm mb-3 p-3 p-md-4 card-section">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="cliente_final">Cliente Final <span class="req">*</span></label>
                    <input type="text" name="cliente_final" id="cliente_final" class="form-control" required maxlength="100" value="{{ old('cliente_final') }}" placeholder="Máx. 100 caracteres" autocomplete="organization">
                </div>
            </div>
        </div>

        @php
            $picklistServiciosItems = $servicios->map(static fn ($s) => [
                'value' => $s->id_servicio,
                'label' => $s->nombre,
            ])->all();
            $picklistPaquetesItems = $paquetes->map(static fn ($p) => [
                'value' => $p->id,
                'label' => \Illuminate\Support\Str::limit($p->nombre, 90),
            ])->all();
            $oldPaquete = old('paquete_id');
        @endphp
        <div class="card border-0 shadow-sm mb-3 p-3 p-md-4 card-section">
            {{-- Misma lógica que el legado: una fila con cuatro columnas (servicios | paquete | ciudad prestación | ciudad solicitud) --}}
            <div class="row g-3 align-items-stretch">
                <div class="col-12 col-md-6 col-lg-3 d-flex flex-column">
                    <label class="form-label" for="picklist-servicios-create-trigger">Servicios (1 a 5) <i class="fas fa-info-circle text-info" title="Elija servicios o un paquete, no ambos" aria-hidden="true"></i></label>
                    @include('panel.partials._picklist-checkbox', [
                        'id' => 'picklist-servicios-create',
                        'name' => 'servicio_ids',
                        'multiple' => true,
                        'max' => 5,
                        'placeholder' => 'Seleccione servicio',
                        'selected' => old('servicio_ids', []),
                        'items' => $picklistServiciosItems,
                        'role' => 'servicios-multiple',
                    ])
                    <p class="form-hint mt-2 mb-0">Haga clic para elegir. Máximo 5 servicios.</p>
                </div>
                <div class="col-12 col-md-6 col-lg-3 d-flex flex-column">
                    <label class="form-label" for="picklist-paquete-create-trigger">Paquetes de servicio</label>
                    @include('panel.partials._picklist-checkbox', [
                        'id' => 'picklist-paquete-create',
                        'name' => 'paquete_id',
                        'multiple' => false,
                        'placeholder' => 'Seleccione paquete',
                        'selected' => $oldPaquete ? [$oldPaquete] : [],
                        'items' => $picklistPaquetesItems,
                        'role' => 'paquete',
                    ])
                    <p class="form-hint mt-2 mb-0">Servicios individuales o un paquete, no ambos en la misma solicitud.</p>
                </div>
                <div class="col-12 col-md-6 col-lg-3 d-flex flex-column">
                    <label class="form-label" for="ciudad_prestacion_servicio">Ciudad donde se prestará el servicio <span class="req">*</span></label>
                    <select name="ciudad_prestacion_servicio" id="ciudad_prestacion_servicio" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c }}" @selected(old('ciudad_prestacion_servicio') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3 d-flex flex-column">
                    <label class="form-label" for="ciudad_solicitud_servicio">Ciudad de solicitud de servicio <span class="req">*</span></label>
                    <select name="ciudad_solicitud_servicio" id="ciudad_solicitud_servicio" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c }}" @selected(old('ciudad_solicitud_servicio') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3 p-3 p-md-4 card-section">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="nombres">Nombres <span class="req">*</span></label>
                    <input type="text" name="nombres" id="nombres" class="form-control" required maxlength="30" value="{{ old('nombres') }}" placeholder="Máx. 30 caracteres" autocomplete="given-name">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="apellidos">Apellidos <span class="req">*</span></label>
                    <input type="text" name="apellidos" id="apellidos" class="form-control" required maxlength="50" value="{{ old('apellidos') }}" placeholder="Máx. 50 caracteres" autocomplete="family-name">
                </div>
                <div class="col-12">
                    <label class="form-label" for="cargo_candidato">Cargo Candidato <span class="req">*</span></label>
                    <input type="text" name="cargo_candidato" id="cargo_candidato" class="form-control" required maxlength="100" value="{{ old('cargo_candidato') }}" placeholder="Máx. 100 caracteres" autocomplete="organization-title">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3 p-3 p-md-4 card-section">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="tipo_identificacion">Tipo de Identificación <span class="req">*</span></label>
                    <select name="tipo_identificacion" id="tipo_identificacion" class="form-select" required>
                        @php $tids = ['CC' => 'Cédula', 'CE' => 'Cédula extranjería', 'PA' => 'Pasaporte', 'NIT' => 'NIT', 'TI' => 'Tarjeta identidad', 'PPT' => 'PPT', 'PEP' => 'PEP']; @endphp
                        <option value="">Seleccione</option>
                        @foreach ($tids as $k => $lab)
                            <option value="{{ $k }}" @selected(old('tipo_identificacion') === $k)>{{ $lab }} ({{ $k }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="numero_documento">Número de Documento <span class="req">*</span></label>
                    <input type="text" name="numero_documento" id="numero_documento" class="form-control" required maxlength="15" value="{{ old('numero_documento') }}" placeholder="Máx. 15 dígitos" inputmode="text" autocomplete="off">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="fecha_expedicion">Fecha de Expedición</label>
                    <input type="date" name="fecha_expedicion" id="fecha_expedicion" class="form-control" value="{{ old('fecha_expedicion') }}">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="lugar_expedicion">Lugar de Expedición</label>
                    <select name="lugar_expedicion" id="lugar_expedicion" class="form-select">
                        <option value="">Seleccione</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c }}" @selected(old('lugar_expedicion') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row g-3 pt-3 mt-2 border-top border-light">
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="telefono_fijo">Teléfono Fijo</label>
                    <input type="text" name="telefono_fijo" id="telefono_fijo" class="form-control" maxlength="10" value="{{ old('telefono_fijo') }}" placeholder="Máx. 10 dígitos" inputmode="numeric">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="celular">Celular <span class="req">*</span></label>
                    <input type="text" name="celular" id="celular" class="form-control" required maxlength="30" value="{{ old('celular') }}" placeholder="Máx. 30 dígitos" inputmode="text" autocomplete="tel">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="ciudad_residencia_evaluado">Ciudad de residencia del evaluado <span class="req">*</span></label>
                    <select name="ciudad_residencia_evaluado" id="ciudad_residencia_evaluado" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c }}" @selected(old('ciudad_residencia_evaluado') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="direccion_residencia">Dirección de residencia <span class="req">*</span></label>
                    <input type="text" name="direccion_residencia" id="direccion_residencia" class="form-control" required maxlength="50" value="{{ old('direccion_residencia') }}" placeholder="Máx. 50 caracteres" autocomplete="street-address">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4 p-3 p-md-4 card-section">
            <label class="form-label" for="comentarios">Comentarios</label>
            <textarea name="comentarios" id="comentarios" class="form-control" rows="4" maxlength="2000" placeholder="Observaciones opcionales…">{{ old('comentarios') }}</textarea>
        </div>

        <div class="d-flex flex-column align-items-center gap-2 mb-4">
            <p class="text-danger small mb-0 text-center px-1 fw-medium {{ ($errors->has('servicio_ids') || $errors->has('paquete_id')) ? '' : 'd-none' }}" id="msgServicioPaquete" role="status">{{ $errors->first('servicio_ids') ?: $errors->first('paquete_id') ?: 'Debe seleccionar entre 1 y 5 servicios O un paquete, pero no ambos.' }}</p>
            <button type="submit" class="btn btn-primary text-uppercase px-5 py-2">Enviar solicitud</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/solicitudes/picklist-checkbox.js') }}?v={{ $picklistAsset }}"></script>
@endpush
