<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Proveedor;

use App\Http\Controllers\Controller;
use App\Domain\Enums\HistorialRespuestaCanal;
use App\Domain\Enums\UserRole;
use App\Http\Requests\ResponderSolicitudProveedorRequest;
use App\Models\RespuestaSolicitud;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Repositories\Contracts\SolicitudRepository;
use App\Services\Solicitud\ProveedorSolicitudRespuestaService;
use App\Services\Solicitud\SolicitudListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    public function __construct(
        private readonly SolicitudListService $listado,
        private readonly SolicitudRepository $solicitudes,
        private readonly ProveedorSolicitudRespuestaService $respuestaProveedor,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Solicitud::class);

        /** @var Usuario $usuario */
        $usuario = $request->user();

        return view('panel.proveedor.solicitudes.index', [
            'solicitudes' => $this->listado->forActor($usuario),
            'detalleRoute' => 'panel.proveedor.solicitudes.show',
        ]);
    }

    public function show(Solicitud $solicitud): View
    {
        $this->authorize('view', $solicitud);

        return view('panel.proveedor.solicitudes.show', [
            'solicitud' => $this->solicitudes->findForDetalle($solicitud->id, HistorialRespuestaCanal::SjProveedor),
        ]);
    }

    public function respuesta(Request $request, Solicitud $solicitud): View
    {
        $this->authorize('view', $solicitud);

        $detallada = $this->solicitudes->findForDetalle($solicitud->id, HistorialRespuestaCanal::SjProveedor);

        /** @var Usuario $usuario */
        $usuario = $request->user();
        $usuario->loadMissing(['proveedor', 'persona']);

        $asignacionSj = RespuestaSolicitud::query()
            ->where('solicitud_id', $detallada->id)
            ->where('canal', HistorialRespuestaCanal::SjProveedor->value)
            ->whereHas('usuario', static fn ($q) => $q->whereIn('id_rol', [
                UserRole::Admin->value,
                UserRole::SuperAdmin->value,
            ]))
            ->orderByDesc('fecha_respuesta')
            ->with(['usuario.persona'])
            ->first();

        $uAsigna = $asignacionSj?->usuario;
        $asignadoPorEtiqueta = '—';
        if ($uAsigna !== null) {
            $login = trim((string) ($uAsigna->usuario ?? ''));
            $correo = trim((string) ($uAsigna->persona?->correo ?? ''));
            $asignadoPorEtiqueta = $login !== '' ? $login : ($correo !== '' ? $correo : '—');
        }

        $puedeResponder = $usuario->can('respondAsProveedor', $detallada);

        return view('panel.proveedor.solicitudes.respuesta', [
            'solicitud' => $detallada,
            'puedeResponder' => $puedeResponder,
            'asignadoPorEtiqueta' => $asignadoPorEtiqueta,
        ]);
    }

    public function respuestaGuardar(ResponderSolicitudProveedorRequest $request, Solicitud $solicitud): RedirectResponse
    {
        $modelo = $this->solicitudes->findOrFail($solicitud->id);

        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->respuestaProveedor->registrarRespuestaProveedor(
            $modelo,
            $usuario,
            $request->nuevaRespuestaTexto(),
            $request->nuevoEstado(),
            $request->archivosPdf(),
        );

        return redirect()
            ->route('panel.proveedor.solicitudes.respuesta', $modelo)
            ->with('status', 'Respuesta registrada correctamente.');
    }
}
