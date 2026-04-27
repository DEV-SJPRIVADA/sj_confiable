@php
    $returnFields = ['per_page' => $perPage, 'q' => $q, 'sort' => $sort, 'dir' => $dir];
@endphp

@can('create', \App\Models\Cliente::class)
<div class="modal fade modal-clientes-legacy" id="modalClienteCrear" tabindex="-1" aria-labelledby="modalClienteCrearLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form method="post" action="{{ route('panel.consultor.clientes.store') }}" id="formClienteCrear" autocomplete="off">
                @csrf
                @foreach ($returnFields as $k => $v)
                    @if ($v !== '' && $v !== null)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <div class="modal-header text-white border-0 rounded-top modal-clientes-legacy__bar">
                    <h2 class="modal-title fs-6 d-flex align-items-center gap-2 mb-0" id="modalClienteCrearLabel">
                        <i class="fas fa-building"></i> Nuevo cliente
                    </h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any() && ! old('editing_cliente_id'))
                        <div class="alert alert-danger small py-2 mb-2">{{ $errors->first() }}</div>
                    @endif
                    @include('panel.consultor.clientes.partials.form-body', [
                        'mode' => 'crear',
                        'idPfx' => 'cc',
                        'editCliente' => null,
                        'tiposCliente' => $tiposCliente,
                        'ciudades' => $ciudades,
                    ])
                </div>
                <div class="modal-footer border-0 d-flex flex-wrap justify-content-end gap-2 pt-2 pb-2 modal-clientes-legacy__footer">
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

@if ($editCliente !== null)
@can('update', $editCliente)
<div class="modal fade modal-clientes-legacy" id="modalClienteEditar" tabindex="-1" aria-labelledby="modalClienteEditarLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form method="post" action="{{ route('panel.consultor.clientes.update', $editCliente) }}" id="formClienteEditar" autocomplete="off">
                @csrf
                @method('PUT')
                @foreach ($returnFields as $k => $v)
                    @if ($v !== '' && $v !== null)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <div class="modal-header text-white border-0 rounded-top modal-clientes-legacy__bar">
                    <h2 class="modal-title fs-6 d-flex align-items-center gap-2 mb-0" id="modalClienteEditarLabel">
                        <i class="fas fa-pen-to-square" aria-hidden="true"></i> Actualizar cliente
                    </h2>
                    <a href="{{ route('panel.consultor.clientes.index', $baseQuery) }}" class="text-white text-decoration-none p-0 lh-1" title="Cerrar" aria-label="Cerrar">
                        <i class="fas fa-times fa-lg"></i>
                    </a>
                </div>
                <div class="modal-body">
                    @if ($errors->any() && old('editing_cliente_id'))
                        <div class="alert alert-danger small py-2 mb-2">{{ $errors->first() }}</div>
                    @endif
                    @include('panel.consultor.clientes.partials.form-body', [
                        'mode' => 'editar',
                        'idPfx' => 'ce',
                        'editCliente' => $editCliente,
                        'tiposCliente' => $tiposCliente,
                        'ciudades' => $ciudades,
                    ])
                </div>
                <div class="modal-footer border-0 d-flex flex-wrap justify-content-end gap-2 pt-2 pb-2 modal-clientes-legacy__footer">
                    <a href="{{ route('panel.consultor.clientes.index', $baseQuery) }}" class="btn btn-outline-light">
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
