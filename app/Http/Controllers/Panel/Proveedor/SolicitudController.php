<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Proveedor;

use App\Http\Controllers\Controller;
use App\Domain\Enums\HistorialRespuestaCanal;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Repositories\Contracts\SolicitudRepository;
use App\Services\Solicitud\SolicitudListService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    public function __construct(
        private readonly SolicitudListService $listado,
        private readonly SolicitudRepository $solicitudes,
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
}
