@php
    /** @var \App\Models\Cliente|null $editCliente */
    /** @var list<string> $tiposCliente */
    /** @var list<string> $ciudades */
    $def = fn (string $key, mixed $legacy = null) => old($key, $legacy);
    $isEdit = $mode === 'editar';
@endphp
<input type="hidden" name="_form" value="{{ $isEdit ? 'editar' : 'crear' }}">
@if($isEdit)
    <input type="hidden" name="editing_cliente_id" value="{{ $def('editing_cliente_id', $editCliente?->id_cliente) }}">
@endif
<div class="row g-2 small">
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_nit">NIT *</label>
        <input type="text" name="nit" id="{{ $idPfx }}_nit" class="form-control" value="{{ $def('nit', $editCliente?->NIT) }}" required inputmode="numeric" pattern="[0-9]+" maxlength="20" autocomplete="off">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_razon_social">Razón social *</label>
        <input type="text" name="razon_social" id="{{ $idPfx }}_razon_social" class="form-control" value="{{ $def('razon_social', $editCliente?->razon_social) }}" required maxlength="255">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_direccion_cliente">Dirección</label>
        <input type="text" name="direccion_cliente" id="{{ $idPfx }}_direccion_cliente" class="form-control" value="{{ $def('direccion_cliente', $editCliente?->direccion_cliente) }}" maxlength="255">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_ciudad_cliente">Ciudad</label>
        <select name="ciudad_cliente" id="{{ $idPfx }}_ciudad_cliente" class="form-select">
            <option value="">—</option>
            @foreach ($ciudades as $ciu)
                <option value="{{ $ciu }}" @selected((string) $def('ciudad_cliente', $editCliente?->ciudad_cliente) === $ciu)>{{ $ciu }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_telefono_cliente">Teléfono</label>
        <input type="text" name="telefono_cliente" id="{{ $idPfx }}_telefono_cliente" class="form-control" value="{{ $def('telefono_cliente', $editCliente?->telefono_cliente) }}" maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_correo_cliente">Correo</label>
        <input type="email" name="correo_cliente" id="{{ $idPfx }}_correo_cliente" class="form-control" value="{{ $def('correo_cliente', $editCliente?->correo_cliente) }}" maxlength="255">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_nombre">Nombre contacto</label>
        <input type="text" name="nombre" id="{{ $idPfx }}_nombre" class="form-control" value="{{ $def('nombre', $editCliente?->nombre) }}" maxlength="100">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_cargo">Cargo contacto</label>
        <input type="text" name="cargo" id="{{ $idPfx }}_cargo" class="form-control" value="{{ $def('cargo', $editCliente?->cargo) }}" maxlength="100">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_tipo_cliente">Tipo cliente *</label>
        <select name="tipo_cliente" id="{{ $idPfx }}_tipo_cliente" class="form-select" required>
            <option value="">—</option>
            @foreach ($tiposCliente as $t)
                <option value="{{ $t }}" @selected((string) $def('tipo_cliente', $editCliente?->tipo_cliente) === $t)>{{ $t }}</option>
            @endforeach
        </select>
    </div>
</div>
