<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Solicitud;
use App\Models\SolicitudUsuario;
use App\Models\Usuario;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('panel.consultor.dashboard', [
            'countUsuarios' => Usuario::query()->where('activo', 1)->count(),
            'countClientes' => Cliente::query()->where('activo', 1)->count(),
            'countAsociados' => Proveedor::query()->count(),
            'countSolicitudesActivas' => Solicitud::query()->where('activo', 1)->count(),
            'countSolicitudesUsuarioPendientes' => SolicitudUsuario::query()->where('estado', 'Pendiente')->count(),
        ]);
    }
}
