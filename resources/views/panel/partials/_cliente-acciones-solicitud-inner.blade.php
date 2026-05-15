{{--
  Acciones panel cliente — paridad flujo viejo:
  1) Estado de solicitud (ResultadoSolicitud), ver detalle, documentos, editar, anular.
--}}
@php
    /** @var \App\Models\Solicitud $s */
    $routeName = $detalleRoute ?? 'panel.cliente.solicitudes.show';
    $filaEsCancelada = str_contains(mb_strtolower(trim((string) ($s->estado ?? ''))), 'cancel');
    $nDocFila = (int) ($s->documentos_count ?? 0);
    $usuario = auth()->user();
    $showUrl = route($routeName, $s);
    $estadoUrl = route('panel.cliente.solicitudes.estado', $s);
    $docUrl = $estadoUrl.'#documentos';
    $canView = $usuario && $usuario->can('view', $s);
    $canOpenEdit = $usuario && ! $filaEsCancelada && $usuario->can('openClienteEdit', $s);
    $canCancel = $usuario && ! $filaEsCancelada && $usuario->can('cancel', $s);
@endphp
<div class="sol-cli-acciones d-inline-flex align-items-center justify-content-center flex-nowrap gap-1 @if ($filaEsCancelada) sol-cli-acciones--inactivos @endif"
     role="group"
     aria-label="Acciones solicitud {{ $s->id }}">
    {{-- 1 Estado de solicitud (legado: portapapeles con check → ResultadoSolicitud) --}}
    @if (! $filaEsCancelada && $canView)
        <a href="{{ $estadoUrl }}" class="sol-cli-acc sol-cli-acc--neutro text-decoration-none" title="Estado de solicitud">
            <i class="fas fa-clipboard-check" aria-hidden="true"></i><span class="visually-hidden">Estado de solicitud</span>
        </a>
    @else
        <span class="sol-cli-acc sol-cli-acc--neutro" title="No disponible"><i class="fas fa-clipboard-check" aria-hidden="true"></i></span>
    @endif

    {{-- 2 Ver detalle --}}
    @if (! $canView || $filaEsCancelada)
        <span class="sol-cli-acc sol-cli-acc--neutro" title="No disponible"><i class="fas fa-search" aria-hidden="true"></i></span>
    @else
        <a href="{{ $showUrl }}" class="sol-cli-acc sol-cli-acc--ver" title="Ver detalle">
            <i class="fas fa-search" aria-hidden="true"></i><span class="visually-hidden">Ver detalle</span>
        </a>
    @endif

    {{-- 3 Adjuntos (clip + _ + número) — enlace a sección documentos del detalle --}}
    @if ($filaEsCancelada)
        <span class="sol-cli-acc sol-cli-acc--clip" title="Sin acceso">
            <i class="fas fa-paperclip sol-cli-acc-clip-icon" aria-hidden="true"></i><span class="sol-cli-acc-clip-sep">_</span><span class="sol-cli-acc-badge-num">{{ $nDocFila }}</span>
        </span>
    @elseif (! $canView)
        <span class="sol-cli-acc sol-cli-acc--clip" title="Sin acceso">
            <i class="fas fa-paperclip sol-cli-acc-clip-icon" aria-hidden="true"></i><span class="sol-cli-acc-clip-sep">_</span><span class="sol-cli-acc-badge-num">{{ $nDocFila }}</span>
        </span>
    @elseif ($nDocFila > 0)
        <a href="{{ $docUrl }}" class="sol-cli-acc sol-cli-acc--clip" title="Adjuntos">
            <i class="fas fa-paperclip sol-cli-acc-clip-icon" aria-hidden="true"></i><span class="sol-cli-acc-clip-sep">_</span><span class="sol-cli-acc-badge-num">{{ $nDocFila }}</span>
        </a>
    @else
        <a href="{{ $docUrl }}" class="sol-cli-acc sol-cli-acc--clip" title="Sin documentos (ir a detalle)">
            <i class="fas fa-paperclip sol-cli-acc-clip-icon" aria-hidden="true"></i><span class="sol-cli-acc-clip-sep">_</span><span class="sol-cli-acc-badge-num">0</span>
        </a>
    @endif

    {{-- 4 Editar (legado: activa y estado distinto de Completado) --}}
    @if ($canOpenEdit)
        <a href="{{ route('panel.cliente.solicitudes.edit', $s) }}" class="sol-cli-acc sol-cli-acc--neutro text-decoration-none" title="Editar solicitud">
            <i class="fas fa-edit" aria-hidden="true"></i><span class="visually-hidden">Editar</span>
        </a>
    @else
        <span class="sol-cli-acc sol-cli-acc--neutro" title="No disponible"><i class="fas fa-edit" aria-hidden="true"></i></span>
    @endif

    {{-- 5 Anular --}}
    @if ($canCancel)
        <form method="post" action="{{ route('panel.cliente.solicitudes.cancel', $s) }}" class="d-inline-block m-0 sol-cli-cancel-form"
              id="sol-cli-cancel-form-{{ $s->id }}">
            @csrf
            <button type="button" class="sol-cli-acc sol-cli-acc--neutro border-0 p-0 bg-transparent sol-cli-cancel-btn"
                    title="Anular solicitud"
                    data-anular-solicitud-trigger
                    data-anular-form-id="sol-cli-cancel-form-{{ $s->id }}"
                    data-solicitud-id="{{ $s->id }}">
                <i class="fas fa-times-circle" aria-hidden="true"></i><span class="visually-hidden">Anular</span>
            </button>
        </form>
    @else
        <span class="sol-cli-acc sol-cli-acc--neutro sol-cli-acc--disabled" title="No disponible" aria-disabled="true"><i class="fas fa-times-circle" aria-hidden="true"></i></span>
    @endif
</div>
