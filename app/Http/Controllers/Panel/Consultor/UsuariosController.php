<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\View\View;

class UsuariosController extends Controller
{
    public function index(): View
    {
        $usuarios = Usuario::query()
            ->with(['rol', 'cliente', 'proveedor'])
            ->orderBy('id_usuario')
            ->paginate(30)
            ->withQueryString();

        return view('panel.consultor.usuarios.index', ['usuarios' => $usuarios]);
    }
}
