@php
    /** @var \App\Models\Proveedor|null $editProveedor */
    /** @var list<string> $ciudades */
    $def = fn (string $key, mixed $legacy = null) => old($key, $legacy);
    $isEdit = $mode === 'editar';
@endphp
<input type="hidden" name="_form" value="{{ $isEdit ? 'editar' : 'crear' }}">
@if($isEdit)
    <input type="hidden" name="editing_proveedor_id" value="{{ $def('editing_proveedor_id', $editProveedor?->id_proveedor) }}">
@endif
<div class="row g-2 small">
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_razon_social_proveedor">Razón social *</label>
        <input type="text" name="razon_social_proveedor" id="{{ $idPfx }}_razon_social_proveedor" class="form-control" value="{{ $def('razon_social_proveedor', $editProveedor?->razon_social_proveedor) }}" required maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_nombre_comercial">Nombre comercial *</label>
        <input type="text" name="nombre_comercial" id="{{ $idPfx }}_nombre_comercial" class="form-control" value="{{ $def('nombre_comercial', $editProveedor?->nombre_comercial) }}" required maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_NIT_proveedor">NIT *</label>
        <input type="text" name="NIT_proveedor" id="{{ $idPfx }}_NIT_proveedor" class="form-control" value="{{ $def('NIT_proveedor', $editProveedor?->NIT_proveedor) }}" required inputmode="numeric" pattern="[0-9]+" maxlength="20" autocomplete="off">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_correo_proveedor">Correo *</label>
        <input type="email" name="correo_proveedor" id="{{ $idPfx }}_correo_proveedor" class="form-control" value="{{ $def('correo_proveedor', $editProveedor?->correo_proveedor) }}" required maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_telefono_proveedor">Teléfono</label>
        <input type="text" name="telefono_proveedor" id="{{ $idPfx }}_telefono_proveedor" class="form-control" value="{{ $def('telefono_proveedor', $editProveedor?->telefono_proveedor) }}" maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_celular_proveedor">Celular *</label>
        <input type="text" name="celular_proveedor" id="{{ $idPfx }}_celular_proveedor" class="form-control" value="{{ $def('celular_proveedor', $editProveedor?->celular_proveedor) }}" required maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_direccion_proveedor">Dirección *</label>
        <input type="text" name="direccion_proveedor" id="{{ $idPfx }}_direccion_proveedor" class="form-control" value="{{ $def('direccion_proveedor', $editProveedor?->direccion_proveedor) }}" required maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_ciudad_proveedor">Ciudad *</label>
        <select name="ciudad_proveedor" id="{{ $idPfx }}_ciudad_proveedor" class="form-select" required>
            <option value="">—</option>
            @foreach ($ciudades as $ciu)
                <option value="{{ $ciu }}" @selected((string) $def('ciudad_proveedor', $editProveedor?->ciudad_proveedor) === $ciu)>{{ $ciu }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_nombre_contacto_proveedor">Nombre contacto *</label>
        <input type="text" name="nombre_contacto_proveedor" id="{{ $idPfx }}_nombre_contacto_proveedor" class="form-control" value="{{ $def('nombre_contacto_proveedor', $editProveedor?->nombre_contacto_proveedor) }}" required maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_cargo_contacto_proveedor">Cargo contacto *</label>
        <input type="text" name="cargo_contacto_proveedor" id="{{ $idPfx }}_cargo_contacto_proveedor" class="form-control" value="{{ $def('cargo_contacto_proveedor', $editProveedor?->cargo_contacto_proveedor) }}" required maxlength="50">
    </div>
</div>
