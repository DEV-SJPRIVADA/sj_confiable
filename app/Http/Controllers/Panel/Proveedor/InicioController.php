<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        return view('panel.proveedor.inicio', [
            'bienvenidaNombre' => (string) ($usuario->usuario ?? ''),
        ]);
    }
}
