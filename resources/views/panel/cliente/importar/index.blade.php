@extends('layouts.app')

@php
    /** @var string $modo */
    /** @var \Illuminate\Support\Collection<int,\App\Models\CatServicio> $servicios */
    $modoActivo = $modo;
@endphp

@section('title', 'Importación Masiva — Cliente')

@push('styles')
<style>
    .import-masiva-page {
        background: #f1f3f5;
        margin: -0.75rem -1.25rem 0 -1.25rem;
        padding: 1.25rem clamp(0.5rem, 2vw, 1.25rem) 2rem;
    }
    @media (max-width: 575.98px) {
        .import-masiva-page { margin: -0.75rem -0.75rem 0 -0.75rem; }
    }
    .import-masiva-page .import-inner { width: 100%; max-width: none; margin-left: auto; margin-right: auto; }
    .import-masiva-page .titulo-principal { font-size: 1.65rem; font-weight: 700; letter-spacing: 0.02em; color: #212529; }
    .import-masiva-page .subtitle { color: #6c757d; font-size: 0.95rem; }
    .import-masiva-page .tipo-toggle .btn-import-tipo {
        font-size: 0.7rem;
        letter-spacing: 0.03em;
        padding: 0.65rem 1rem;
        border: 2px solid #0d6efd;
        white-space: normal;
        line-height: 1.25;
    }
    @media (min-width: 576px) {
        .import-masiva-page .tipo-toggle .btn-import-tipo { font-size: 0.72rem; }
    }
    @media (min-width: 992px) {
        .import-masiva-page .tipo-toggle .btn-import-tipo { font-size: 0.75rem; }
    }
    .import-masiva-page .tipo-toggle .btn-import-tipo.active {
        background: #0d6efd !important;
        color: #fff !important;
    }
    .import-masiva-page .tipo-toggle .btn-import-tipo:not(.active) {
        background: #fff;
        color: #0d6efd;
    }
    .import-masiva-page .cartel-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.35rem;
        box-shadow: 0 0.12rem 0.35rem rgba(0, 0, 0, 0.06);
        height: 100%;
    }
    .import-masiva-page .cartel-card h3 { font-size: 1rem; font-weight: 600; }
    .import-masiva-page .cartel-eval-titulo { font-size: 0.9375rem; font-weight: 600; color: #495057; }
    .import-masiva-page .cartel-card.instr-evaluados-caja {
        background: #f8f9fa;
    }
    .import-masiva-page .table-ejemplo { font-size: 0.8125rem; }
    .import-masiva-page .table-ejemplo thead th { white-space: nowrap; background: #e9ecef !important; }
</style>
@endpush

@section('content')
<div class="import-masiva-page">
<div class="import-inner">

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <p class="mb-3 text-center text-md-start small">
        <a href="{{ route('panel.cliente.solicitudes.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Volver a solicitudes</a>
    </p>

    <header class="text-center mb-4">
        <h1 class="titulo-principal mb-2">Importación Masiva</h1>
        <p class="subtitle mb-3 mb-lg-4">Elige el tipo de importación y sube tu archivo Excel (.xlsx).</p>

        <div class="d-flex justify-content-center tipo-toggle mb-4">
            <div class="btn-group flex-wrap shadow-sm rounded-pill overflow-hidden border border-primary border-opacity-50" role="group" aria-label="Tipo de importación">
                <a href="{{ route('panel.cliente.importar', ['modo' => 'solicitudes']) }}"
                   class="btn btn-import-tipo px-3 py-3 rounded-0 fw-semibold {{ $modoActivo === 'evaluados' ? '' : 'active' }}"
                   role="tab">SOLICITUDES (INDIVIDUALES/PAQUETES)</a>
                <a href="{{ route('panel.cliente.importar', ['modo' => 'evaluados']) }}"
                   class="btn btn-import-tipo px-3 py-3 rounded-0 fw-semibold {{ $modoActivo === 'evaluados' ? 'active' : '' }}"
                   role="tab">EVALUADOS MÚLTIPLES POR SOLICITUD</a>
            </div>
        </div>
    </header>

    <div class="row g-4 g-lg-4 align-items-stretch mb-4">
        <div class="col-12 col-lg-6">
            <div class="cartel-card p-3 p-md-4 h-100">
                @if ($modoActivo === 'solicitudes')
                    <h3 class="mb-3">Subir archivo</h3>
                    <form method="post" action="{{ route('panel.cliente.importar.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="modo" value="solicitudes">
                        <label class="form-label" for="archivo-solicitudes">Selecciona un archivo Excel (.xlsx):</label>
                        <input type="file" name="archivo" id="archivo-solicitudes" class="form-control form-control-sm mb-3" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required>
                        @error('archivo')
                            <div class="text-danger small mb-3">{{ $message }}</div>
                        @enderror
                        @error('modo')
                            <div class="text-danger small mb-3">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-primary w-100 py-3 text-uppercase fw-semibold rounded-3">Subir archivo</button>
                    </form>
                @else
                    <p class="cartel-eval-titulo mb-3">Importar Evaluados y crear Solicitud</p>
                    <form method="post" action="{{ route('panel.cliente.importar.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="modo" value="evaluados">
                        <label class="form-label" for="servicio_id">Selecciona el servicio:</label>
                        <select name="servicio_id" id="servicio_id" class="form-select form-select-sm mb-3" required>
                            <option value="">-- Selecciona servicio --</option>
                            @foreach ($servicios as $svc)
                                <option value="{{ $svc->id_servicio }}" @selected((string) old('servicio_id') === (string) $svc->id_servicio)>{{ $svc->nombre }}</option>
                            @endforeach
                        </select>
                        @error('servicio_id')
                            <div class="text-danger small mb-3">{{ $message }}</div>
                        @enderror

                        <label class="form-label" for="archivo-evaluados">Archivo de evaluados (Excel .xlsx):</label>
                        <input type="file" name="archivo" id="archivo-evaluados" class="form-control form-control-sm mb-3" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required>
                        @error('archivo')
                            <div class="text-danger small mb-3">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn btn-primary w-100 py-3 text-uppercase fw-semibold rounded-3">Importar evaluados</button>
                    </form>
                @endif
            </div>
        </div>
        <div class="col-12 col-lg-6">
            @if ($modoActivo === 'solicitudes')
                <div class="cartel-card p-3 p-md-4 h-100">
                    <h3 class="mb-3">Instrucciones</h3>
                    <div class="small text-body">
                        <ul class="ps-3 mb-3 mb-lg-4">
                            <li class="mb-2">El archivo debe estar en formato <strong>.XLSX</strong>.</li>
                            <li class="mb-2">Debe contener <strong>16 columnas</strong> con información completa.</li>
                            <li class="mb-2">Registra completamente los campos <strong class="req" style="color:#c0392b;">obligatorios *</strong>.</li>
                            <li class="mb-2">Las fechas deben estar en formato <strong>YYYY-MM-DD</strong> o <strong>DD/MM/YYYY</strong>.</li>
                        </ul>
                        <p class="small mb-0">Puedes descargar una <a href="{{ route('panel.cliente.importar.plantilla-solicitudes') }}" class="link-primary fw-medium text-decoration-underline">plantilla XLSX aquí.</a></p>
                    </div>
                </div>
            @else
                <div class="cartel-card instr-evaluados-caja p-3 p-md-4 h-100 small text-body">
                    <strong class="d-block mb-3">Instrucciones</strong>
                    <ul class="ps-3 mb-3">
                        <li class="mb-2">Importa hasta <strong>20 evaluados</strong> por solicitud.</li>
                        <li class="mb-2">
                            Cabeceras esperadas:
                            <span class="text-danger fw-medium">Nombres, Apellidos, TipoID, NumDoc, FechaExp, LugarExp, TelFijo, Celular, CiudadResidencia, Direccion, Cargo.</span>
                        </li>
                        <li class="mb-2">Formato de fecha: <strong>YYYY-MM-DD</strong>.</li>
                        <li class="mb-2">Se creará una nueva solicitud con el servicio elegido y se vincularán los evaluados del archivo.</li>
                    </ul>
                    <p class="mb-0">Plantilla XLSX para evaluados: <a href="{{ route('panel.cliente.importar.plantilla-evaluados') }}" class="link-primary fw-semibold text-decoration-underline">descargar</a>.</p>
                </div>
            @endif
        </div>
    </div>

    <section class="{{ $modoActivo === 'evaluados' ? 'd-none' : '' }}">
        <h3 class="h6 fw-bold mb-3">Ejemplo del Formato (Solicitudes)</h3>
        <div class="table-responsive rounded-legacy bg-white border shadow-sm">
            <table class="table table-legacy table-sm table-bordered table-ejemplo mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Empresa</th>
                        <th>NIT</th>
                        <th>Servicio</th>
                        <th>CiudadPresta</th>
                        <th>CiudadSolic</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>TipoID</th>
                        <th>NumDoc</th>
                        <th>FechaExp</th>
                        <th>LugarExp</th>
                        <th>TelFijo</th>
                        <th>Celular</th>
                        <th>CiudadResidencia</th>
                        <th>Direccion</th>
                        <th>Comentarios</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Empresa X</td>
                        <td>987654</td>
                        <td>Bases de datos</td>
                        <td>Bogotá</td>
                        <td>Medellín</td>
                        <td>Ana</td>
                        <td>Gómez</td>
                        <td>CC</td>
                        <td>12345678</td>
                        <td>2020-01-15</td>
                        <td>Cali</td>
                        <td>6012345678</td>
                        <td>3001234567</td>
                        <td>Cali</td>
                        <td>Carrera 1 #2-3</td>
                        <td>Sin comentarios</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="{{ $modoActivo === 'evaluados' ? '' : 'd-none' }}">
        <h3 class="h6 fw-bold mb-3">Ejemplo del Formato (Evaluados)</h3>
        <div class="table-responsive rounded-legacy bg-white border shadow-sm">
            <table class="table table-legacy table-sm table-bordered table-ejemplo mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>TipoID</th>
                        <th>NumDoc</th>
                        <th>FechaExp</th>
                        <th>LugarExp</th>
                        <th>TelFijo</th>
                        <th>Celular</th>
                        <th>CiudadResidencia</th>
                        <th>Direccion</th>
                        <th>Cargo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ana</td>
                        <td>Gómez</td>
                        <td>CC</td>
                        <td>998877</td>
                        <td>2025-01-01</td>
                        <td>Bogotá</td>
                        <td>60111223344</td>
                        <td>3207654321</td>
                        <td>Cali</td>
                        <td>Carrera 8 #12-34</td>
                        <td>Analista Jr.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

</div>
</div>
@endsection
