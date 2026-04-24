<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Http\Requests\AsignarSolicitudRequest;
use App\Models\Proveedor;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Repositories\Contracts\SolicitudRepository;
use App\Services\Solicitud\SolicitudAsignacionService;
use App\Services\Solicitud\SolicitudListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    public function __construct(
        private readonly SolicitudListService $listado,
        private readonly SolicitudRepository $solicitudes,
        private readonly SolicitudAsignacionService $asignacion,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Solicitud::class);

        /** @var Usuario $usuario */
        $usuario = $request->user();

        return view('panel.consultor.solicitudes.index', [
            'solicitudes' => $this->listado->forActor($usuario),
            'detalleRoute' => 'panel.consultor.solicitudes.show',
        ]);
    }

    public function show(Solicitud $solicitud): View
    {
        $this->authorize('view', $solicitud);
        $solicitud = $this->solicitudes->findForDetalle($solicitud->id);

        $proveedores = Proveedor::query()
            ->orderBy('razon_social_proveedor')
            ->get();

        return view('panel.consultor.solicitudes.show', [
            'solicitud' => $solicitud,
            'proveedores' => $proveedores,
        ]);
    }

    public function asignar(AsignarSolicitudRequest $request, Solicitud $solicitud): RedirectResponse
    {
        $this->authorize('assignToProveedor', $solicitud);

        /** @var Usuario $actor */
        $actor = $request->user();
        $this->asignacion->asignar(
            $solicitud,
            $request->idProveedor(),
            $request->clienteFinal(),
            $request->tipoCliente(),
            (int) $actor->id_usuario,
        );

        return redirect()
            ->route('panel.consultor.solicitudes.show', $solicitud)
            ->with('status', 'Solicitud asignada al asociado y notificaciones registradas.');
    }
}
