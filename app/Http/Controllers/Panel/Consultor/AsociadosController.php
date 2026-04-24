<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\View\View;

class AsociadosController extends Controller
{
    public function index(): View
    {
        $proveedores = Proveedor::query()
            ->orderBy('razon_social_proveedor')
            ->paginate(30)
            ->withQueryString();

        return view('panel.consultor.asociados.index', ['proveedores' => $proveedores]);
    }
}
