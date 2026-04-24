<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\View\View;

class ClientesController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::query()
            ->orderBy('razon_social')
            ->paginate(30)
            ->withQueryString();

        return view('panel.consultor.clientes.index', ['clientes' => $clientes]);
    }
}
