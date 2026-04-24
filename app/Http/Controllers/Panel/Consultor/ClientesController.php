<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreClienteRequest;
use App\Http\Requests\Catalog\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\Catalog\ClienteCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientesController extends Controller
{
    public function __construct(
        private readonly ClienteCatalogService $clientes,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Cliente::class);

        $clientes = Cliente::query()
            ->orderBy('razon_social')
            ->paginate(30)
            ->withQueryString();

        return view('panel.consultor.clientes.index', ['clientes' => $clientes]);
    }

    public function create(): View
    {
        $this->authorize('create', Cliente::class);

        return view('panel.consultor.clientes.create');
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $this->authorize('create', Cliente::class);
        $this->clientes->crear($this->mapear($request->validated()));

        return redirect()
            ->route('panel.consultor.clientes.index')
            ->with('status', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente): View
    {
        $this->authorize('update', $cliente);

        return view('panel.consultor.clientes.edit', ['cliente' => $cliente]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $this->authorize('update', $cliente);
        $this->clientes->actualizar($cliente, $this->mapear($request->validated()));

        return redirect()
            ->route('panel.consultor.clientes.index')
            ->with('status', 'Cliente actualizado correctamente.');
    }

    public function toggleActivo(Request $request, Cliente $cliente): RedirectResponse
    {
        $this->authorize('toggleActivo', $cliente);
        $this->clientes->alternarActivo($cliente);

        return redirect()
            ->route('panel.consultor.clientes.index')
            ->with('status', 'Estado de cliente (y sus usuarios) actualizado.');
    }

    /**
     * @param  array<string, mixed>  $v
     * @return array<string, mixed>
     */
    private function mapear(array $v): array
    {
        return [
            'nit' => $v['nit'],
            'razon_social' => $v['razon_social'],
            'direccion_cliente' => $v['direccion_cliente'] ?? null,
            'ciudad_cliente' => $v['ciudad_cliente'] ?? null,
            'telefono_cliente' => $v['telefono_cliente'] ?? null,
            'correo_cliente' => $v['correo_cliente'] ?? null,
            'nombre' => $v['nombre'] ?? null,
            'cargo' => $v['cargo'] ?? null,
            'tipo_cliente' => $v['tipo_cliente'],
        ];
    }
}
