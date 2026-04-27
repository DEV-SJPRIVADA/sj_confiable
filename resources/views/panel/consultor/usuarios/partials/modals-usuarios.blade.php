@php
    /** @var \Illuminate\Database\Eloquent\Collection $roles */
    /** @var \Illuminate\Database\Eloquent\Collection $clientes */
    /** @var \Illuminate\Database\Eloquent\Collection $proveedores */
    /** @var \App\Models\Usuario|null $editUsuario */
    $returnFields = ['per_page' => $perPage, 'q' => $q, 'sort' => $sort, 'dir' => $dir];
    $proveedorRolId = \App\Domain\Enums\UserRole::Proveedor->value;
@endphp

@can('create', \App\Models\Usuario::class)
<div class="modal fade modal-usuarios-legacy" id="modalUsuarioCrear" tabindex="-1" aria-labelledby="modalUsuarioCrearLabel" aria-hidden="true" data-bs-backdrop="static" data-rol-proveedor="{{ (int) $proveedorRolId }}">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form method="post" action="{{ route('panel.consultor.usuarios.store') }}" id="formUsuarioCrear" autocomplete="off">
                @csrf
                @foreach ($returnFields as $k => $v)
                    @if ($v !== '' && $v !== null)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <div class="modal-header text-white border-0 rounded-top modal-usuarios-legacy__bar">
                    <h2 class="modal-title fs-6 d-flex align-items-center gap-2 mb-0" id="modalUsuarioCrearLabel">
                        <i class="fas fa-user-plus"></i> Nuevo usuario
                    </h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any() && ! old('editing_user_id'))
                        <div class="alert alert-danger small py-2 mb-2">{{ $errors->first() }}</div>
                    @endif
                    @include('panel.consultor.usuarios.partials.form-body', [
                        'mode' => 'crear',
                        'idPfx' => 'c',
                        'editUsuario' => null,
                        'roles' => $roles,
                        'clientes' => $clientes,
                        'proveedores' => $proveedores,
                    ])
                </div>
                <div class="modal-footer border-0 d-flex flex-wrap justify-content-end gap-2 pt-2 pb-2 modal-usuarios-legacy__footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-outline-light">
                        <i class="fas fa-floppy-disk me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@if ($editUsuario !== null)
@can('update', $editUsuario)
<div class="modal fade modal-usuarios-legacy" id="modalUsuarioEditar" tabindex="-1" aria-labelledby="modalUsuarioEditarLabel" aria-hidden="true" data-bs-backdrop="static" data-rol-proveedor="{{ (int) $proveedorRolId }}">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form method="post" action="{{ route('panel.consultor.usuarios.update', $editUsuario) }}" id="formUsuarioEditar" autocomplete="off">
                @csrf
                @method('PUT')
                @foreach ($returnFields as $k => $v)
                    @if ($v !== '' && $v !== null)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <div class="modal-header text-white border-0 rounded-top modal-usuarios-legacy__bar">
                    <h2 class="modal-title fs-6 d-flex align-items-center gap-2 mb-0" id="modalUsuarioEditarLabel">
                        <i class="fas fa-user-cog"></i> Actualizar usuario
                    </h2>
                    <a href="{{ route('panel.consultor.usuarios.index', $baseQuery) }}" class="text-white text-decoration-none p-0 lh-1" title="Cerrar" aria-label="Cerrar">
                        <i class="fas fa-times fa-lg"></i>
                    </a>
                </div>
                <div class="modal-body">
                    @if ($errors->any() && old('editing_user_id'))
                        <div class="alert alert-danger small py-2 mb-2">{{ $errors->first() }}</div>
                    @endif
                    @include('panel.consultor.usuarios.partials.form-body', [
                        'mode' => 'editar',
                        'idPfx' => 'e',
                        'editUsuario' => $editUsuario,
                        'roles' => $roles,
                        'clientes' => $clientes,
                        'proveedores' => $proveedores,
                    ])
                </div>
                <div class="modal-footer border-0 d-flex flex-wrap justify-content-end gap-2 pt-2 pb-2 modal-usuarios-legacy__footer">
                    <a href="{{ route('panel.consultor.usuarios.index', $baseQuery) }}" class="btn btn-outline-light">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </a>
                    <button type="submit" class="btn btn-outline-light">
                        <i class="fas fa-floppy-disk me-1"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endif
