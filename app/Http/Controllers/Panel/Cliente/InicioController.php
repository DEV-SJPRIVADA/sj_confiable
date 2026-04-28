<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Services\Panel\ClienteInicioService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function __construct(
        private readonly ClienteInicioService $inicio,
    ) {}

    public function index(Request $request): View
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            abort(403);
        }
        $this->authorize('viewAny', Solicitud::class);

        return view('panel.cliente.inicio', $this->inicio->build($request, $usuario));
    }
}
