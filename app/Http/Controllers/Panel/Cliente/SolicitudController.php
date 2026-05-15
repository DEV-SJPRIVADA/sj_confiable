<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteSolicitudRequest;
use App\Http\Requests\UpdateClienteSolicitudRequest;
use App\Models\CatServicio;
use App\Models\PaqueteServicio;
use App\Models\Solicitud;
use App\Domain\Enums\HistorialRespuestaCanal;
use App\Models\Usuario;
use App\Repositories\Contracts\SolicitudRepository;
use App\Services\Panel\NotificacionClienteService;
use App\Services\Solicitud\ClienteSolicitudActualizacionService;
use App\Services\Solicitud\ClienteSolicitudCancelacionService;
use App\Services\Solicitud\ClienteSolicitudCreacionService;
use App\Services\Solicitud\SolicitudListService;
use App\Support\CiudadesColombia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    public function __construct(
        private readonly SolicitudListService $listado,
        private readonly SolicitudRepository $solicitudes,
        private readonly NotificacionClienteService $notificacionesCliente,
    ) {}

    public function create(Request $request): View
    {
        $this->authorize('create', Solicitud::class);

        return view('panel.cliente.solicitudes.create', [
            'servicios' => CatServicio::query()->orderBy('nombre')->get(),
            'paquetes' => PaqueteServicio::query()->orderBy('nombre')->get(),
            'ciudades' => CiudadesColombia::opciones(),
        ]);
    }

    public function store(StoreClienteSolicitudRequest $request, ClienteSolicitudCreacionService $crear): RedirectResponse
    {
        $this->authorize('create', Solicitud::class);

        /** @var Usuario $usuario */
        $usuario = $request->user();
        $payload = array_merge($request->validated(), [
            'comentarios' => $request->input('comentarios'),
        ]);
        $solicitud = $crear->crear($usuario, $payload);

        return redirect()
            ->route('panel.cliente.solicitudes.estado', $solicitud)
            ->with('status', 'Solicitud registrada correctamente.');
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Solicitud::class);

        /** @var Usuario $usuario */
        $usuario = $request->user();

        return view('panel.cliente.solicitudes.index', [
            'solicitudes' => $this->listado->forActor($usuario),
            'detalleRoute' => 'panel.cliente.solicitudes.show',
        ]);
    }

    public function show(Request $request, Solicitud $solicitud): View
    {
        $this->authorize('view', $solicitud);

        /** @var Usuario $actor */
        $actor = $request->user();
        $this->notificacionesCliente->marcarLeidaAlSeguirEnlace(
            $actor,
            (int) $request->query('nc', 0),
            (int) $solicitud->id,
        );

        return view('panel.cliente.solicitudes.show', [
            'solicitud' => $this->solicitudes->findForDetalle($solicitud->id, HistorialRespuestaCanal::ClienteSj),
        ]);
    }

    /**
     * Vista «Estado de solicitud» (legado ResultadoSolicitud): abre desde el primer icono del listado.
     */
    public function estado(Request $request, Solicitud $solicitud): View
    {
        $this->authorize('view', $solicitud);

        /** @var Usuario $actor */
        $actor = $request->user();
        $this->notificacionesCliente->marcarLeidaAlSeguirEnlace(
            $actor,
            (int) $request->query('nc', 0),
            (int) $solicitud->id,
        );

        return view('panel.cliente.solicitudes.estado', [
            'solicitud' => $this->solicitudes->findForEstadoCliente($solicitud->id),
        ]);
    }

    public function edit(Solicitud $solicitud): View
    {
        $this->authorize('openClienteEdit', $solicitud);

        $solicitud->load(['serviciosPivote']);

        return view('panel.cliente.solicitudes.edit', [
            'solicitud' => $solicitud,
            'servicios' => CatServicio::query()->orderBy('nombre')->get(),
            'paquetes' => PaqueteServicio::query()->orderBy('nombre')->get(),
            'ciudades' => CiudadesColombia::opciones(),
            'servicioIdsSeleccionados' => $solicitud->serviciosPivote->pluck('id_servicio')->map(fn ($id) => (int) $id)->all(),
        ]);
    }

    public function update(
        UpdateClienteSolicitudRequest $request,
        Solicitud $solicitud,
        ClienteSolicitudActualizacionService $actualizar,
    ): RedirectResponse {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $actualizar->actualizar($usuario, $solicitud, $request->validated());

        return redirect()
            ->route('panel.cliente.solicitudes.estado', $solicitud)
            ->with('status', 'Solicitud actualizada correctamente.');
    }

    public function cancel(Request $request, Solicitud $solicitud, ClienteSolicitudCancelacionService $cancelar): RedirectResponse
    {
        $this->authorize('cancel', $solicitud);

        $cancelar->cancelar($solicitud, $request->user());

        return redirect()
            ->route('panel.cliente.solicitudes.index')
            ->with('status', 'Solicitud cancelada correctamente. El equipo SJ fue notificado.');
    }
}
