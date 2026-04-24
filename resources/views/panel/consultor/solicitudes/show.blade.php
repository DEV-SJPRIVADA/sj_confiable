@extends('layouts.app')

@section('title', 'Solicitud #'.$solicitud->id)

@section('content')
    <p class="mb-3">
        <a href="{{ route('panel.consultor.solicitudes.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Volver al listado</a>
    </p>
    @include('panel.solicitudes._detalle', ['solicitud' => $solicitud])
    @can('assignToProveedor', $solicitud)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Asignar a asociado de negocios (mediación)</div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger small">{{ $errors->first() }}</div>
                @endif
                <p class="text-muted small">Asigna la solicitud a un asociado; se registra en historial y se generan notificaciones al solicitante y a usuarios del asociado (mismo flujo que el PHP legado, sin reenviar correos SMTP en esta fase).</p>
                <form method="post" action="{{ route('panel.consultor.solicitudes.asignar', $solicitud) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="id_proveedor" class="form-label">Asociado de negocios</label>
                        <select name="id_proveedor" id="id_proveedor" class="form-select" required>
                            <option value="">— Seleccione —</option>
                            @foreach($proveedores as $p)
                                <option value="{{ $p->id_proveedor }}" @selected((int) $solicitud->id_proveedor === (int) $p->id_proveedor)>
                                    {{ $p->razon_social_proveedor }} ({{ $p->nombre_comercial }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cliente_final" class="form-label">Cliente final (opcional)</label>
                        <input type="text" class="form-control" name="cliente_final" id="cliente_final" maxlength="150" value="{{ old('cliente_final', $solicitud->cliente_final) }}">
                    </div>
                    <div class="mb-3">
                        <label for="tipo_cliente" class="form-label">Tipo cliente (opcional)</label>
                        <select name="tipo_cliente" id="tipo_cliente" class="form-select">
                            <option value="">— N/C —</option>
                            <option value="Interno" @selected(old('tipo_cliente', $solicitud->tipo_cliente) === 'Interno')>Interno</option>
                            <option value="Externo" @selected(old('tipo_cliente', $solicitud->tipo_cliente) === 'Externo')>Externo</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Asignar y poner en proceso</button>
                </form>
            </div>
        </div>
    @endcan
@endsection
