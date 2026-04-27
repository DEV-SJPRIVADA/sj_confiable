@php
    $p = $editUsuario?->persona;
    $def = fn (string $key, mixed $legacy = null) => old($key, $legacy);
    $isEdit = $mode === 'editar';
@endphp
<input type="hidden" name="_form" value="{{ $isEdit ? 'editar' : 'crear' }}">
@if($isEdit)
    <input type="hidden" name="editing_user_id" value="{{ $def('editing_user_id', $editUsuario?->id_usuario) }}">
@endif
<div class="row g-2 small">
    <div class="col-12">
        <p class="form-label text-muted small mb-1">Permisos para el usuario:</p>
        <div class="d-flex flex-wrap gap-3">
            <div class="form-check form-switch us-modal-form-switch">
                <input class="form-check-input" type="checkbox" name="permiso_ver_documentos" id="{{ $idPfx }}_pv" value="1" @checked($def('permiso_ver_documentos', $isEdit && $editUsuario ? (bool) $editUsuario->permiso_ver_documentos : false))>
                <label class="form-check-label" for="{{ $idPfx }}_pv">Ver documentos</label>
            </div>
            <div class="form-check form-switch us-modal-form-switch">
                <input class="form-check-input" type="checkbox" name="permiso_subir_documentos" id="{{ $idPfx }}_ps" value="1" @checked($def('permiso_subir_documentos', $isEdit && $editUsuario ? (bool) $editUsuario->permiso_subir_documentos : false))>
                <label class="form-check-label" for="{{ $idPfx }}_ps">Subir documentos</label>
            </div>
            <div class="form-check form-switch us-modal-form-switch">
                <input class="form-check-input" type="checkbox" name="permiso_crear_solicitudes" id="{{ $idPfx }}_pc" value="1" @checked($def('permiso_crear_solicitudes', $isEdit && $editUsuario ? (bool) $editUsuario->permiso_crear_solicitudes : false))>
                <label class="form-check-label" for="{{ $idPfx }}_pc">Crear solicitudes</label>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_nombre">Nombre *</label>
        <input type="text" name="nombre" id="{{ $idPfx }}_nombre" class="form-control" value="{{ $def('nombre', $p?->nombre) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_paterno">Primer apellido *</label>
        <input type="text" name="paterno" id="{{ $idPfx }}_paterno" class="form-control" value="{{ $def('paterno', $p?->paterno) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_materno">Segundo apellido</label>
        <input type="text" name="materno" id="{{ $idPfx }}_materno" class="form-control" value="{{ $def('materno', $p?->materno) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_telefono">Teléfono *</label>
        <input type="text" name="telefono" id="{{ $idPfx }}_telefono" class="form-control" value="{{ $def('telefono', $p?->telefono) }}" required maxlength="15">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_correo">Correo *</label>
        <input type="email" name="correo" id="{{ $idPfx }}_correo" class="form-control" value="{{ $def('correo', $p?->correo) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_identificacion">Identificación *</label>
        <input type="text" name="identificacion" id="{{ $idPfx }}_identificacion" class="form-control" value="{{ $def('identificacion', $p?->identificacion) }}" required maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_usuario">Usuario *</label>
        <input type="text" name="usuario" id="{{ $idPfx }}_usuario" class="form-control" value="{{ $def('usuario', $editUsuario?->usuario) }}" required maxlength="245" autocomplete="off">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_id_rol">Rol de usuario *</label>
        <select name="id_rol" id="{{ $idPfx }}_id_rol" class="form-select id-rol-select" data-wrap-cliente="{{ $idPfx }}_wrap_cliente" data-wrap-proveedor="{{ $idPfx }}_wrap_proveedor" data-id-cliente="{{ $idPfx }}_id_cliente" data-id-proveedor="{{ $idPfx }}_id_proveedor" required>
            <option value="">—</option>
            @foreach ($roles as $r)
                <option value="{{ $r->id_rol }}" @selected((int) $def('id_rol', $editUsuario?->id_rol) === (int) $r->id_rol)>
                    {{ $r->nombre }} ({{ $r->id_rol }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <div class="row g-2">
            <div class="col-12 col-md-6 position-relative">
                <label class="form-label" for="{{ $idPfx }}_password">{{ $isEdit ? 'Nueva contraseña' : 'Contraseña *' }}</label>
                <div class="input-group">
                    <input type="password" name="password" id="{{ $idPfx }}_password" class="form-control" @if(!$isEdit) required @endif @if(!$isEdit) minlength="8" maxlength="15" @else maxlength="15" @endif placeholder="{{ $isEdit ? '(dejar vacío para no cambiar)' : '' }}" autocomplete="new-password" @if($isEdit) data-pw-optional="1" @endif>
                    <button class="btn btn-outline-secondary" type="button" data-toggle-pw="#{{ $idPfx }}_password" title="Ver"><i class="fas fa-eye"></i></button>
                </div>
                <ul class="list-unstyled small mt-1 mb-0 text-muted" id="{{ $idPfx }}_pw_rules">
                    <li><i class="fas fa-check text-success opacity-0 me-1 ru-len"></i>Entre 8 y 15 caracteres</li>
                    <li><i class="fas fa-check text-success opacity-0 me-1 ru-up"></i>Al menos una mayúscula</li>
                    <li><i class="fas fa-check text-success opacity-0 me-1 ru-lo"></i>Al menos una minúscula</li>
                    <li><i class="fas fa-check text-success opacity-0 me-1 ru-num"></i>Al menos un número</li>
                    <li><i class="fas fa-check text-success opacity-0 me-1 ru-spl"></i>Al menos un carácter especial</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div id="{{ $idPfx }}_wrap_cliente">
            <label class="form-label" for="{{ $idPfx }}_id_cliente">Cliente *</label>
            <select name="id_cliente" id="{{ $idPfx }}_id_cliente" class="form-select">
                <option value="">—</option>
                @foreach ($clientes as $c)
                    <option value="{{ $c->id_cliente }}" @selected((int) $def('id_cliente', $editUsuario?->id_cliente ?? 0) === (int) $c->id_cliente)>
                        {{ $c->razon_social }}
                    </option>
                @endforeach
            </select>
        </div>
        <div id="{{ $idPfx }}_wrap_proveedor" class="d-none">
            <label class="form-label" for="{{ $idPfx }}_id_proveedor">Asociado *</label>
            <select name="id_proveedor" id="{{ $idPfx }}_id_proveedor" class="form-select">
                <option value="">—</option>
                @foreach ($proveedores as $pr)
                    <option value="{{ $pr->id_proveedor }}" @selected((int) $def('id_proveedor', $editUsuario?->id_proveedor ?? 0) === (int) $pr->id_proveedor)>
                        {{ $pr->razon_social_proveedor }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_celular">Celular *</label>
        <input type="text" name="celular" id="{{ $idPfx }}_celular" class="form-control" value="{{ $def('celular', $p?->celular) }}" required maxlength="15">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_ciudad">Ciudad *</label>
        <input type="text" name="ciudad" id="{{ $idPfx }}_ciudad" class="form-control" value="{{ $def('ciudad', $isEdit ? $editUsuario?->ciudad : 'Cali') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $idPfx }}_direccion">Dirección</label>
        <input type="text" name="direccion" id="{{ $idPfx }}_direccion" class="form-control" value="{{ $def('direccion', $p?->direccion) }}" maxlength="100">
    </div>
</div>
<div class="form-check mt-2">
    <input class="form-check-input" type="checkbox" name="notificar_email" id="{{ $idPfx }}_notif" value="1" @checked(old('notificar_email'))>
    <label class="form-check-label small" for="{{ $idPfx }}_notif">¿Deseas notificar por correo al usuario los cambios realizados?</label>
</div>
