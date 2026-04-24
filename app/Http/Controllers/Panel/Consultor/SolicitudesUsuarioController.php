<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Models\SolicitudUsuario;
use Illuminate\View\View;

class SolicitudesUsuarioController extends Controller
{
    public function index(): View
    {
        $solicitudes = SolicitudUsuario::query()
            ->with(['cliente', 'solicitante', 'usuarioResponde'])
            ->orderByDesc('fecha_solicitud')
            ->paginate(30)
            ->withQueryString();

        return view('panel.consultor.solicitudes-usuarios.index', ['solicitudes' => $solicitudes]);
    }
}
