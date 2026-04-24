<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreProveedorRequest;
use App\Http\Requests\Catalog\UpdateProveedorRequest;
use App\Models\Proveedor;
use App\Services\Catalog\ProveedorCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AsociadosController extends Controller
{
    public function __construct(
        private readonly ProveedorCatalogService $proveedores,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Proveedor::class);

        $proveedores = Proveedor::query()
            ->orderBy('razon_social_proveedor')
            ->paginate(30)
            ->withQueryString();

        return view('panel.consultor.asociados.index', ['proveedores' => $proveedores]);
    }

    public function create(): View
    {
        $this->authorize('create', Proveedor::class);

        return view('panel.consultor.asociados.create');
    }

    public function store(StoreProveedorRequest $request): RedirectResponse
    {
        $this->authorize('create', Proveedor::class);
        $this->proveedores->crear($request->validated());

        return redirect()
            ->route('panel.consultor.asociados.index')
            ->with('status', 'Asociado de negocios creado correctamente.');
    }

    public function edit(Proveedor $proveedor): View
    {
        $this->authorize('update', $proveedor);

        return view('panel.consultor.asociados.edit', ['proveedor' => $proveedor]);
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor): RedirectResponse
    {
        $this->authorize('update', $proveedor);
        $this->proveedores->actualizar($proveedor, $request->validated());

        return redirect()
            ->route('panel.consultor.asociados.index')
            ->with('status', 'Asociado actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $this->authorize('delete', $proveedor);
        try {
            $this->proveedores->eliminar($proveedor);
        } catch (\DomainException $e) {
            return redirect()
                ->route('panel.consultor.asociados.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('panel.consultor.asociados.index')
            ->with('status', 'Asociado eliminado.');
    }
}
