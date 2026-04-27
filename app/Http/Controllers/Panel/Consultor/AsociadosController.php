<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreProveedorRequest;
use App\Http\Requests\Catalog\UpdateProveedorRequest;
use App\Models\Proveedor;
use App\Models\Usuario;
use App\Services\Catalog\ProveedorCatalogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AsociadosController extends Controller
{
    public function __construct(
        private readonly ProveedorCatalogService $proveedores,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Proveedor::class);

        $perPage = (int) $request->input('per_page', 50);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 50;
        }

        $dir = strtolower((string) $request->input('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sort = (string) $request->input('sort', 'razon_social');
        $q = $request->string('q')->trim()->toString();

        $query = Proveedor::query()->select('t_proveedores.*');

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function (Builder $w) use ($like): void {
                $w->where('t_proveedores.NIT_proveedor', 'like', $like)
                    ->orWhere('t_proveedores.razon_social_proveedor', 'like', $like)
                    ->orWhere('t_proveedores.nombre_comercial', 'like', $like)
                    ->orWhere('t_proveedores.ciudad_proveedor', 'like', $like)
                    ->orWhere('t_proveedores.correo_proveedor', 'like', $like)
                    ->orWhere('t_proveedores.telefono_proveedor', 'like', $like)
                    ->orWhere('t_proveedores.celular_proveedor', 'like', $like)
                    ->orWhere('t_proveedores.direccion_proveedor', 'like', $like)
                    ->orWhere('t_proveedores.nombre_contacto_proveedor', 'like', $like)
                    ->orWhere('t_proveedores.cargo_contacto_proveedor', 'like', $like);
            });
        }

        $this->applyProveedorSort($query, $sort, $dir);

        $proveedores = $query
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        $actor = $this->authed();
        $wantsCrear = $request->query('open_modal') === 'crear' || (string) $request->session()->get('open_modal') === 'crear';
        $wantsEditar = $request->query('open_modal') === 'editar' || (string) $request->session()->get('open_modal') === 'editar';
        $editId = (int) ($request->session()->get('edit_proveedor_id') ?: $request->query('edit_proveedor', 0));
        if ($editId === 0) {
            $editId = (int) old('editing_proveedor_id', 0);
        }

        $editProveedor = null;
        if ($wantsEditar && $editId > 0) {
            $candidato = Proveedor::query()->find($editId);
            if ($candidato !== null) {
                Gate::forUser($actor)->authorize('update', $candidato);
                $editProveedor = $candidato;
            }
        }

        $ciudades = $this->ciudadesParaVista($editProveedor, (string) old('ciudad_proveedor', ''));

        return view('panel.consultor.asociados.index', array_merge(
            $this->catalogoFormularioModal($ciudades),
            [
                'proveedores' => $proveedores,
                'perPage' => $perPage,
                'q' => $q,
                'sort' => $sort,
                'dir' => $dir,
                'autoshowModalCrear' => $wantsCrear && Gate::forUser($actor)->allows('create', Proveedor::class),
                'autoshowModalEditar' => $wantsEditar && $editProveedor !== null,
                'editProveedor' => $editProveedor,
            ],
        ));
    }

    public function create(Request $request): RedirectResponse
    {
        $this->authorize('create', Proveedor::class);

        return redirect()->route('panel.consultor.asociados.index', array_merge(
            $this->onlyIndexQuery($request),
            ['open_modal' => 'crear'],
        ));
    }

    public function store(StoreProveedorRequest $request): RedirectResponse
    {
        $this->authorize('create', Proveedor::class);
        $this->proveedores->crear($request->validated());

        return redirect()
            ->route('panel.consultor.asociados.index')
            ->with('status', 'Asociado de negocios creado correctamente.');
    }

    public function edit(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $this->authorize('update', $proveedor);

        return redirect()->route('panel.consultor.asociados.index', array_merge(
            $this->onlyIndexQuery($request),
            [
                'open_modal' => 'editar',
                'edit_proveedor' => $proveedor->id_proveedor,
            ],
        ));
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor): RedirectResponse
    {
        $this->authorize('update', $proveedor);
        $this->proveedores->actualizar($proveedor, $request->validated());

        return redirect()
            ->route('panel.consultor.asociados.index')
            ->with('status', 'Asociado de negocios actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $this->authorize('delete', $proveedor);
        try {
            $this->proveedores->eliminar($proveedor);
        } catch (\DomainException $e) {
            return back()
                ->with('error', $e->getMessage());
        }

        return back()
            ->with('status', 'Asociado eliminado.');
    }

    private function authed(): Usuario
    {
        /** @var Usuario $u */
        $u = auth()->user();

        return $u;
    }

    /**
     * @return array<string, string|int>
     */
    private function onlyIndexQuery(Request $request): array
    {
        return array_filter(
            $request->only(['per_page', 'q', 'sort', 'dir']),
            static fn (mixed $v): bool => $v !== null && $v !== '',
        );
    }

    private function applyProveedorSort(Builder $query, string $sort, string $dir): void
    {
        $map = [
            'id_proveedor' => 't_proveedores.id_proveedor',
            'nit' => 't_proveedores.NIT_proveedor',
            'razon_social' => 't_proveedores.razon_social_proveedor',
            'comercial' => 't_proveedores.nombre_comercial',
            'ciudad' => 't_proveedores.ciudad_proveedor',
            'correo' => 't_proveedores.correo_proveedor',
            'contacto' => 't_proveedores.nombre_contacto_proveedor',
            'cargo' => 't_proveedores.cargo_contacto_proveedor',
        ];
        if (isset($map[$sort])) {
            $query->orderBy($map[$sort], $dir);

            return;
        }
        $query->orderBy('t_proveedores.razon_social_proveedor', 'asc');
    }

    /**
     * @return array{ciudades: list<string>}
     */
    private function catalogoFormularioModal(array $ciudades): array
    {
        return [
            'ciudades' => $ciudades,
        ];
    }

    private function ciudadesReferencia(): array
    {
        return [
            'Armenia', 'Barranquilla', 'Bogotá', 'Bucaramanga', 'Cali', 'Cartagena', 'Cúcuta', 'Ibagué', 'Manizales',
            'Medellín', 'Montería', 'Palmira', 'Pasto', 'Pereira', 'Popayán', 'Santa Marta', 'Tuluá', 'Valledupar', 'Villavicencio',
        ];
    }

    private function ciudadesParaVista(?Proveedor $editProveedor, string $oldCiudad): array
    {
        $list = $this->ciudadesReferencia();
        $current = $oldCiudad !== '' ? $oldCiudad : $editProveedor?->ciudad_proveedor;
        if ($current !== null && $current !== '' && ! in_array($current, $list, true)) {
            $list = array_values(array_unique(array_merge([$current], $list)));
        }
        usort($list, static function (string $a, string $b): int {
            return strcasecmp($a, $b);
        });

        return $list;
    }
}
