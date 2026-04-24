<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ResponderSolicitudUsuarioRequest;
use App\Models\SolicitudUsuario;
use App\Services\Catalog\SolicitudUsuarioRespuestaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SolicitudesUsuarioController extends Controller
{
    public function __construct(
        private readonly SolicitudUsuarioRespuestaService $respuestas,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', SolicitudUsuario::class);

        $solicitudes = SolicitudUsuario::query()
            ->with(['cliente', 'solicitante', 'usuarioResponde'])
            ->orderByDesc('fecha_solicitud')
            ->paginate(30)
            ->withQueryString();

        return view('panel.consultor.solicitudes-usuarios.index', ['solicitudes' => $solicitudes]);
    }

    public function responder(ResponderSolicitudUsuarioRequest $request, SolicitudUsuario $solicitudUsuario): RedirectResponse
    {
        $this->authorize('respond', $solicitudUsuario);

        $actor = $request->user();
        if (! $actor) {
            abort(401);
        }

        try {
            $this->respuestas->responder(
                $solicitudUsuario,
                $actor,
                $request->string('estado')->toString(),
                $request->string('comentario')->toString(),
            );
        } catch (\DomainException|\InvalidArgumentException $e) {
            return redirect()
                ->route('panel.consultor.solicitudes-usuarios.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('panel.consultor.solicitudes-usuarios.index')
            ->with('status', 'Solicitud respondida.');
    }
}
