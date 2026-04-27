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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    public function __construct(
        private readonly SolicitudRepository $solicitudes,
        private readonly SolicitudAsignacionService $asignacion,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Solicitud::class);

        $vista = $request->input('vista', 'activas');
        $activo = $vista === 'inactivas' ? 0 : 1;

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $q = trim((string) $request->input('q', ''));

        $sort = (string) $request->input('sort', 'fecha');
        $dir = strtolower((string) $request->input('dir', 'desc'));
        if (! in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'desc';
        }
        $sortColumns = ['id', 'evaluado', 'documento', 'ciudad', 'fecha', 'estado', 'cliente', 'enviado'];
        if (! in_array($sort, $sortColumns, true)) {
            $sort = 'fecha';
        }

        $solicitudes = $this->solicitudes->paginateForConsultor(
            $activo,
            $perPage,
            $q,
            $sort,
            $dir,
        );

        return view('panel.consultor.solicitudes.index', [
            'solicitudes' => $solicitudes,
            'vista' => $vista === 'inactivas' ? 'inactivas' : 'activas',
            'perPage' => $perPage,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
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
