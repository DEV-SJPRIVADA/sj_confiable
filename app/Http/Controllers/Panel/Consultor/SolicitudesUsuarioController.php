<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ResponderSolicitudUsuarioRequest;
use App\Models\SolicitudUsuario;
use App\Models\Usuario;
use App\Services\Catalog\SolicitudUsuarioRespuestaService;
use App\Services\Panel\NotificacionConsultorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudesUsuarioController extends Controller
{
    public function __construct(
        private readonly SolicitudUsuarioRespuestaService $respuestas,
        private readonly NotificacionConsultorService $notificacionesConsultor,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', SolicitudUsuario::class);

        /** @var Usuario|null $actor */
        $actor = $request->user();
        if ($actor instanceof Usuario) {
            $this->notificacionesConsultor->marcarLeidaAlSeguirEnlace(
                $actor,
                (int) $request->query('cn', 0),
                null,
            );
        }

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
        $sortKeys = ['id', 'cliente', 'solicitante', 'tipo', 'estado', 'fecha'];
        if (! in_array($sort, $sortKeys, true)) {
            $sort = 'fecha';
        }

        $query = SolicitudUsuario::query()
            ->with(['cliente', 'solicitante', 'usuarioResponde'])
            ->leftJoin('t_clientes as c', 't_solicitudes_usuario.id_cliente', '=', 'c.id_cliente')
            ->leftJoin('t_usuarios as u_sol', 't_solicitudes_usuario.id_usuario_solicitante', '=', 'u_sol.id_usuario')
            ->select('t_solicitudes_usuario.*');

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function (Builder $w) use ($like, $q): void {
                $w->where('t_solicitudes_usuario.tipo', 'like', $like)
                    ->orWhere('t_solicitudes_usuario.estado', 'like', $like)
                    ->orWhere('t_solicitudes_usuario.datos_usuario', 'like', $like)
                    ->orWhere('c.razon_social', 'like', $like)
                    ->orWhere('u_sol.usuario', 'like', $like);
                if (preg_match('/^\d+$/', $q) === 1) {
                    $w->orWhere('t_solicitudes_usuario.id_solicitud', (int) $q);
                }
            });
        }

        $this->applySolicitudUsuarioSort($query, $sort, $dir);

        $solicitudes = $query
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        return view('panel.consultor.solicitudes-usuarios.index', [
            'solicitudes' => $solicitudes,
            'perPage' => $perPage,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    private function applySolicitudUsuarioSort(Builder $query, string $sort, string $dir): void
    {
        $d = $dir === 'desc' ? 'desc' : 'asc';
        if ($sort === 'id') {
            $query->orderBy('t_solicitudes_usuario.id_solicitud', $d);
        } elseif ($sort === 'cliente') {
            $query->orderBy('c.razon_social', $d);
        } elseif ($sort === 'solicitante') {
            $query->orderBy('u_sol.usuario', $d);
        } elseif ($sort === 'tipo') {
            $query->orderBy('t_solicitudes_usuario.tipo', $d);
        } elseif ($sort === 'estado') {
            $query->orderBy('t_solicitudes_usuario.estado', $d);
        } else {
            $query->orderBy('t_solicitudes_usuario.fecha_solicitud', $d);
        }
        if ($sort !== 'id') {
            $query->orderBy('t_solicitudes_usuario.id_solicitud', 'desc');
        }
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
