@php
    /** @var string|null $listaEstilo ejemplo: cliente-legacy */
    $listaEstilo = $listaEstilo ?? null;
    $esListaClienteLegacy = $listaEstilo === 'cliente-legacy';
    $esListaProveedorLegacy = $listaEstilo === 'proveedor-legacy';

    $claseLegacyFilaProveedor = static function (?string $estadoRaw): string {
        $estadoClass = \App\Support\LegacySolicitudFilaEstado::claseCss($estadoRaw);

        return $estadoClass !== ''
            ? 'tabla-solicitudes-prov-fila '.$estadoClass
            : 'tabla-solicitudes-prov-fila';
    };

    $ciudadListaProveedor = static function (\App\Models\Solicitud $s): string {
        $a = trim((string) ($s->ciudad_solicitud_servicio ?? ''));
        if ($a !== '') {
            return $a;
        }

        return trim((string) ($s->ciudad_residencia_evaluado ?? '')) ?: '—';
    };

    $claseLegacyFila = static function (?string $estadoRaw): string {
        $estadoClass = \App\Support\LegacySolicitudFilaEstado::claseCss($estadoRaw);

        return $estadoClass !== ''
            ? 'tabla-solicitudes-cli-fila '.$estadoClass
            : 'tabla-solicitudes-cli-fila';
    };

    $iconOrdenCabecera = static function (): string {
        return '<span class="ms-1 tabla-solicitudes-cli-th-sort" aria-hidden="true"><i class="fas fa-sort fa-xs opacity-75"></i></span>';
    };
@endphp
<div class="table-responsive rounded-legacy bg-white @if ($esListaClienteLegacy) tabla-solicitudes-cli-wrap @endif @if ($esListaProveedorLegacy) tabla-solicitudes-prov-wrap @endif">
    <table class="table table-legacy table-sm table-bordered {{ $esListaClienteLegacy || $esListaProveedorLegacy ? '' : 'table-hover' }} align-middle mb-0 @if ($esListaClienteLegacy) tabla-solicitudes-cli @endif @if ($esListaProveedorLegacy) tabla-solicitudes-prov @endif" id="tablaPanelSolicitudes">
        <thead class="@if ($esListaClienteLegacy) tabla-solicitudes-cli-thead @else table-light @endif">
        <tr>
            @if ($esListaClienteLegacy)
                <th class="text-nowrap"># Solicitud {!! $iconOrdenCabecera() !!}</th>
                <th>Servicios {!! $iconOrdenCabecera() !!}</th>
                <th>Nombre Completo {!! $iconOrdenCabecera() !!}</th>
                <th>Ciudad de solicitud {!! $iconOrdenCabecera() !!}</th>
                <th>Estado {!! $iconOrdenCabecera() !!}</th>
                <th>Analista {!! $iconOrdenCabecera() !!}</th>
                <th class="text-center">Evaluados {!! $iconOrdenCabecera() !!}</th>
                <th>Documento {!! $iconOrdenCabecera() !!}</th>
                <th>Regional {!! $iconOrdenCabecera() !!}</th>
                <th class="text-nowrap">Celular {!! $iconOrdenCabecera() !!}</th>
                <th class="text-center text-nowrap">Acciones {!! $iconOrdenCabecera() !!}</th>
            @elseif ($esListaProveedorLegacy)
                <th class="text-nowrap">ID</th>
                <th>Evaluado</th>
                <th>Documento</th>
                <th>Ciudad</th>
                <th class="text-nowrap">Fecha Creacion</th>
                <th>Estado</th>
                <th class="small">Enviado por</th>
                <th>Cliente</th>
                <th class="text-center text-nowrap">Acciones</th>
            @else
                <th>ID</th>
                <th>Evaluado</th>
                <th>Documento</th>
                <th>Archivos</th>
                <th>Evaluados</th>
                <th>Usuario</th>
                <th>Cliente</th>
                <th>Servicio</th>
                <th>Estado</th>
                <th>Creacion</th>
                <th class="text-nowrap">Acciones</th>
            @endif
        </tr>
        </thead>
        <tbody id="tbodyPanelSolicitudes">
        @forelse ($solicitudes as $s)
            <tr @if ($esListaClienteLegacy) class="{{ $claseLegacyFila((string) $s->estado) }}" @elseif ($esListaProveedorLegacy) class="{{ $claseLegacyFilaProveedor((string) $s->estado) }}" @endif>
                @if ($esListaClienteLegacy)
                    <td class="text-nowrap">
                        @isset($detalleRoute)
                            <a href="{{ route($detalleRoute, $s) }}" class="text-decoration-none" style="color: #0d6efd;">{{ $s->id }}</a>
                        @else
                            {{ $s->id }}
                        @endisset
                    </td>
                    <td class="servicios-legado-cell">
                        <span class="d-inline-flex align-items-start gap-2 flex-wrap">
                            <i class="fas fa-graduation-cap text-primary mt-1 flex-shrink-0" style="font-size:0.85rem;" title="Servicios" aria-hidden="true"></i>
                            <span>{{ $s->labelServiciosContratados() }}</span>
                        </span>
                    </td>
                    <td class="tabla-solicitudes-cli-evaluado">{{ \Illuminate\Support\Str::upper(trim(($s->nombres ?? '').' '.($s->apellidos ?? ''))) ?: '—' }}</td>
                    <td>{{ $s->ciudad_solicitud_servicio ?? '—' }}</td>
                    <td>{{ $s->estado }}</td>
                    <td class="small text-break">{{ $s->creador?->usuario ?? '—' }}</td>
                    <td class="text-center">{{ $s->evaluados_count }}</td>
                    <td class="text-nowrap">{{ $s->tipo_identificacion }} {{ $s->numero_documento }}</td>
                    <td>{{ $s->ciudad_prestacion_servicio ?? '—' }}</td>
                    <td class="text-nowrap">{{ $s->celular ?? '—' }}</td>
                    <td class="text-center text-nowrap sol-cli-acciones-td">
                        @isset($detalleRoute)
                            @include('panel.partials._cliente-acciones-solicitud-inner', ['s' => $s, 'detalleRoute' => $detalleRoute])
                        @else
                            <span class="text-muted">—</span>
                        @endisset
                    </td>
                @elseif ($esListaProveedorLegacy)
                    @php
                        $detailUrl = isset($detalleRoute) ? route($detalleRoute, $s) : '#';
                        $urlGestionRespuesta = isset($proveedorGestionRespuestaRoute)
                            ? route($proveedorGestionRespuestaRoute, $s)
                            : $detailUrl.'#panel-solicitud-historial';
                        $envPor = trim((string) ($s->creador?->persona?->correo ?? ''));
                        if ($envPor === '') {
                            $envPor = trim((string) ($s->creador?->usuario ?? ''));
                        }
                        if ($envPor === '') {
                            $envPor = '—';
                        }
                    @endphp
                    <td class="text-nowrap fw-medium">
                        @isset($detalleRoute)
                            <button type="button" class="btn btn-link p-0 m-0 align-baseline text-decoration-none fw-medium" style="color: #0d6efd;" data-bs-toggle="modal" data-bs-target="#modalDetalleSolicitud{{ $s->id }}">{{ $s->id }}</button>
                        @else
                            {{ $s->id }}
                        @endisset
                    </td>
                    <td>{{ trim(($s->nombres ?? '').' '.($s->apellidos ?? '')) ?: '—' }}</td>
                    <td class="text-nowrap small">{{ trim(($s->tipo_identificacion ?? '').' '.($s->numero_documento ?? '')) ?: '—' }}</td>
                    <td>{{ $ciudadListaProveedor($s) }}</td>
                    <td class="text-nowrap small">{{ $s->fecha_creacion?->format('d/m/Y h:i A') ?? '—' }}</td>
                    <td>{{ $s->estado ?? '—' }}</td>
                    <td class="small text-break">{{ $envPor }}</td>
                    <td>{{ $s->creador?->cliente?->razon_social ?? '—' }}</td>
                    <td class="text-center text-nowrap">
                        @isset($detalleRoute)
                            <div class="d-inline-flex align-items-center justify-content-center gap-1 flex-wrap">
                                <button type="button" class="prov-solic-round-btn" data-bs-toggle="modal" data-bs-target="#modalDetalleSolicitud{{ $s->id }}" title="Ver detalle de la solicitud"><span class="visually-hidden">Ver detalle de la solicitud</span><i class="fas fa-eye" aria-hidden="true"></i></button>
                                <a href="{{ $urlGestionRespuesta }}" class="prov-solic-round-btn prov-solic-round-btn--muted" title="Gestionar respuesta"><span class="visually-hidden">Gestionar respuesta</span><i class="fas fa-reply fa-flip-horizontal" aria-hidden="true"></i></a>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endisset
                    </td>
                @else
                    <td>
                        @isset($detalleRoute)
                            <a href="{{ route($detalleRoute, $s) }}" class="text-decoration-none" style="color: #0d6efd;">{{ $s->id }}</a>
                        @else
                            {{ $s->id }}
                        @endisset
                    </td>
                    <td>{{ $s->nombres }} {{ $s->apellidos }}</td>
                    <td>{{ $s->tipo_identificacion }} {{ $s->numero_documento }}</td>
                    <td>{{ $s->documentos_count }}</td>
                    <td>{{ $s->evaluados_count }}</td>
                    <td>{{ $s->creador?->usuario ?? '—' }}</td>
                    <td>{{ $s->creador?->cliente?->razon_social ?? '—' }}</td>
                    <td>{{ $s->labelServiciosContratados() }}</td>
                    <td>{{ $s->estado }}</td>
                    <td>{{ $s->fecha_creacion?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="text-nowrap">
                        @isset($detalleRoute)
                            <a class="btn btn-sm btn-primary" href="{{ route($detalleRoute, $s) }}"><i class="fas fa-eye me-1 d-none d-md-inline" aria-hidden="true"></i>Ver detalle</a>
                        @else
                            <span class="text-muted">—</span>
                        @endisset
                    </td>
                @endif
            </tr>
        @empty
            <tr class="tabla-solicitudes-msg-vacio">
                <td colspan="{{ $esListaProveedorLegacy ? '9' : '11' }}" class="text-center text-muted">No hay solicitudes para mostrar.</td>
            </tr>
        @endforelse
        @if ($esListaClienteLegacy)
            <tr id="tbodyClienteSinResultadosBusqueda" class="tabla-solicitudes-msg-busqueda d-none">
                <td colspan="11" class="text-center text-muted small py-3">Ningún registro coincide con la búsqueda.</td>
            </tr>
        @endif
        @if ($esListaProveedorLegacy)
            <tr id="tbodyProvSinResultadosBusqueda" class="tabla-solicitudes-prov-msg-busqueda d-none">
                <td colspan="9" class="text-center text-muted small py-3">Ningún registro coincide con la búsqueda.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
