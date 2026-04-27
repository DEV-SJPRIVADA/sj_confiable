@php
    $returnFields = ['per_page' => $perPage, 'q' => $q, 'sort' => $sort, 'dir' => $dir];
@endphp

@can('create', \App\Models\Proveedor::class)
<div class="modal fade modal-asociados-legacy" id="modalAsociadoCrear" tabindex="-1" aria-labelledby="modalAsociadoCrearLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form method="post" action="{{ route('panel.consultor.asociados.store') }}" id="formAsociadoCrear" autocomplete="off">
                @csrf
                @foreach ($returnFields as $k => $v)
                    @if ($v !== '' && $v !== null)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <div class="modal-header text-white border-0 rounded-top modal-asociados-legacy__bar">
                    <h2 class="modal-title fs-6 d-flex align-items-center gap-2 mb-0" id="modalAsociadoCrearLabel">
                        <i class="fas fa-handshake" aria-hidden="true"></i> Nuevo asociado de negocio
                    </h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any() && ! old('editing_proveedor_id'))
                        <div class="alert alert-danger small py-2 mb-2">{{ $errors->first() }}</div>
                    @endif
                    @include('panel.consultor.asociados.partials.form-body', [
                        'mode' => 'crear',
                        'idPfx' => 'ac',
                        'editProveedor' => null,
                        'ciudades' => $ciudades,
                    ])
                </div>
                <div class="modal-footer border-0 d-flex flex-wrap justify-content-end gap-2 pt-2 pb-2 modal-asociados-legacy__footer">
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

@if ($editProveedor !== null)
@can('update', $editProveedor)
<div class="modal fade modal-asociados-legacy" id="modalAsociadoEditar" tabindex="-1" aria-labelledby="modalAsociadoEditarLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form method="post" action="{{ route('panel.consultor.asociados.update', $editProveedor) }}" id="formAsociadoEditar" autocomplete="off">
                @csrf
                @method('PUT')
                @foreach ($returnFields as $k => $v)
                    @if ($v !== '' && $v !== null)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <div class="modal-header text-white border-0 rounded-top modal-asociados-legacy__bar">
                    <h2 class="modal-title fs-6 d-flex align-items-center gap-2 mb-0" id="modalAsociadoEditarLabel">
                        <i class="fas fa-pen-to-square" aria-hidden="true"></i> Editar asociado de negocio
                    </h2>
                    <a href="{{ route('panel.consultor.asociados.index', $baseQuery) }}" class="text-white text-decoration-none p-0 lh-1" title="Cerrar" aria-label="Cerrar">
                        <i class="fas fa-times fa-lg"></i>
                    </a>
                </div>
                <div class="modal-body">
                    @if ($errors->any() && old('editing_proveedor_id'))
                        <div class="alert alert-danger small py-2 mb-2">{{ $errors->first() }}</div>
                    @endif
                    @include('panel.consultor.asociados.partials.form-body', [
                        'mode' => 'editar',
                        'idPfx' => 'ae',
                        'editProveedor' => $editProveedor,
                        'ciudades' => $ciudades,
                    ])
                </div>
                <div class="modal-footer border-0 d-flex flex-wrap justify-content-end gap-2 pt-2 pb-2 modal-asociados-legacy__footer">
                    <a href="{{ route('panel.consultor.asociados.index', $baseQuery) }}" class="btn btn-outline-light">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </a>
                    <button type="submit" class="btn btn-outline-light">
                        <i class="fas fa-floppy-disk me-1"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endif
