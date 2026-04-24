<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Cliente;

use App\Http\Controllers\Controller;
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

        return view('panel.cliente.solicitudes.index', [
            'solicitudes' => $this->listado->forActor($usuario),
            'detalleRoute' => 'panel.cliente.solicitudes.show',
        ]);
    }

    public function show(Solicitud $solicitud): View
    {
        $this->authorize('view', $solicitud);

        return view('panel.cliente.solicitudes.show', [
            'solicitud' => $this->solicitudes->findForDetalle($solicitud->id),
        ]);
    }
}
