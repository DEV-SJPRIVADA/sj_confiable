<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreClienteRequest;
use App\Http\Requests\Catalog\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Services\Catalog\ClienteCatalogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientesController extends Controller
{
    public function __construct(
        private readonly ClienteCatalogService $clientes,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Cliente::class);

        $perPage = (int) $request->input('per_page', 50);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 50;
        }

        $dir = strtolower((string) $request->input('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sort = (string) $request->input('sort', 'razon_social');
        $q = $request->string('q')->trim()->toString();

        $query = Cliente::query()->select('t_clientes.*');

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function (Builder $w) use ($like): void {
                $w->where('t_clientes.NIT', 'like', $like)
                    ->orWhere('t_clientes.razon_social', 'like', $like)
                    ->orWhere('t_clientes.direccion_cliente', 'like', $like)
                    ->orWhere('t_clientes.ciudad_cliente', 'like', $like)
                    ->orWhere('t_clientes.telefono_cliente', 'like', $like)
                    ->orWhere('t_clientes.correo_cliente', 'like', $like)
                    ->orWhere('t_clientes.nombre', 'like', $like)
                    ->orWhere('t_clientes.cargo', 'like', $like)
                    ->orWhere('t_clientes.tipo_cliente', 'like', $like);
            });
        }

        $this->applyClienteSort($query, $sort, $dir);

        $clientes = $query
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        $actor = $this->authed();
        $wantsCrear = $request->query('open_modal') === 'crear' || (string) $request->session()->get('open_modal') === 'crear';
        $wantsEditar = $request->query('open_modal') === 'editar' || (string) $request->session()->get('open_modal') === 'editar';
        $editId = (int) ($request->session()->get('edit_cliente_id') ?: $request->query('edit_cliente', 0));
        if ($editId === 0) {
            $editId = (int) old('editing_cliente_id', 0);
        }

        $editCliente = null;
        if ($wantsEditar && $editId > 0) {
            $candidato = Cliente::query()->find($editId);
            if ($candidato !== null) {
                Gate::forUser($actor)->authorize('update', $candidato);
                $editCliente = $candidato;
            }
        }

        $ciudades = $this->ciudadesParaVista($editCliente, (string) old('ciudad_cliente', ''));
        $tiposCliente = $this->tiposParaVista($editCliente, (string) old('tipo_cliente', ''));

        return view('panel.consultor.clientes.index', array_merge($this->catalogoFormularioModal($ciudades, $tiposCliente), [
            'clientes' => $clientes,
            'perPage' => $perPage,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
            'autoshowModalCrear' => $wantsCrear && Gate::forUser($actor)->allows('create', Cliente::class),
            'autoshowModalEditar' => $wantsEditar && $editCliente !== null,
            'editCliente' => $editCliente,
        ]));
    }

    public function create(Request $request): RedirectResponse
    {
        $this->authorize('create', Cliente::class);

        return redirect()->route('panel.consultor.clientes.index', array_merge(
            $this->onlyIndexQuery($request),
            ['open_modal' => 'crear'],
        ));
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $this->authorize('create', Cliente::class);
        $this->clientes->crear($this->mapear($request->validated()));

        return redirect()
            ->route('panel.consultor.clientes.index')
            ->with('status', 'Cliente creado correctamente.');
    }

    public function edit(Request $request, Cliente $cliente): RedirectResponse
    {
        $this->authorize('update', $cliente);

        return redirect()->route('panel.consultor.clientes.index', array_merge(
            $this->onlyIndexQuery($request),
            [
                'open_modal' => 'editar',
                'edit_cliente' => $cliente->id_cliente,
            ],
        ));
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

        return back()
            ->with('status', 'Estado de cliente (y sus usuarios) actualizado.');
    }

    private function applyClienteSort(Builder $query, string $sort, string $dir): void
    {
        $map = [
            'id_cliente' => 't_clientes.id_cliente',
            'nit' => 't_clientes.NIT',
            'razon_social' => 't_clientes.razon_social',
            'direccion' => 't_clientes.direccion_cliente',
            'ciudad' => 't_clientes.ciudad_cliente',
            'telefono' => 't_clientes.telefono_cliente',
            'correo' => 't_clientes.correo_cliente',
            'nombre' => 't_clientes.nombre',
            'cargo' => 't_clientes.cargo',
            'tipo' => 't_clientes.tipo_cliente',
            'activo' => 't_clientes.activo',
        ];
        if (isset($map[$sort])) {
            $query->orderBy($map[$sort], $dir);

            return;
        }
        $query->orderBy('t_clientes.razon_social', 'asc');
    }

    /**
     * @param  array<string, mixed>  $v
     * @return array<string, mixed>
     */
    private function mapear(array $v): array
    {
        return [
            'nit' => (int) $v['nit'],
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

    /**
     * @return array{tiposCliente: list<string>, ciudades: list<string>}
     */
    private function catalogoFormularioModal(array $ciudades, array $tiposCliente): array
    {
        return [
            'tiposCliente' => $tiposCliente,
            'ciudades' => $ciudades,
        ];
    }

    /**
     * @return list<string>
     */
    private function ciudadesReferencia(): array
    {
        return [
            'Armenia', 'Barranquilla', 'Bogotá', 'Bucaramanga', 'Cali', 'Cartagena', 'Cúcuta', 'Ibagué', 'Manizales',
            'Medellín', 'Montería', 'Palmira', 'Pasto', 'Pereira', 'Popayán', 'Santa Marta', 'Tuluá', 'Valledupar', 'Villavicencio',
        ];
    }

    /**
     * @return list<string>
     */
    private function tiposClienteReferencia(): array
    {
        return ['Grupo', 'Subgrupo', 'Individual', 'Interno', 'Externo'];
    }

    /**
     * @return list<string>
     */
    private function tiposParaVista(?Cliente $editCliente, string $oldTipo): array
    {
        $list = $this->tiposClienteReferencia();
        $current = $oldTipo !== '' ? $oldTipo : $editCliente?->tipo_cliente;
        if ($current !== null && $current !== '' && ! in_array($current, $list, true)) {
            $list = array_values(array_unique(array_merge([$current], $list)));
        }
        usort($list, static function (string $a, string $b): int {
            return strcasecmp($a, $b);
        });

        return $list;
    }

    private function ciudadesParaVista(?Cliente $editCliente, string $oldCiudad): array
    {
        $list = $this->ciudadesReferencia();
        $current = $oldCiudad !== '' ? $oldCiudad : $editCliente?->ciudad_cliente;
        if ($current !== null && $current !== '' && ! in_array($current, $list, true)) {
            $list = array_values(array_unique(array_merge([$current], $list)));
        }
        usort($list, static function (string $a, string $b): int {
            return strcasecmp($a, $b);
        });

        return $list;
    }
}
