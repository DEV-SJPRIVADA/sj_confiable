<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreUsuarioGestionRequest;
use App\Http\Requests\Catalog\UpdateUsuarioGestionRequest;
use App\Models\CatRol;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Usuario;
use App\Services\Catalog\UsuarioGestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UsuariosController extends Controller
{
    public function __construct(
        private readonly UsuarioGestionService $usuarios,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Usuario::class);

        $perPage = (int) $request->input('per_page', 50);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 50;
        }

        $dir = strtolower((string) $request->input('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sort = (string) $request->input('sort', 'id_usuario');
        $q = $request->string('q')->trim()->toString();

        $query = Usuario::query()
            ->select('t_usuarios.*')
            ->leftJoin('t_persona', 't_persona.id_persona', '=', 't_usuarios.id_persona')
            ->leftJoin('t_clientes', 't_clientes.id_cliente', '=', 't_usuarios.id_cliente')
            ->leftJoin('t_proveedores', 't_proveedores.id_proveedor', '=', 't_usuarios.id_proveedor')
            ->with(['rol', 'cliente', 'proveedor', 'persona']);

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function (Builder $w) use ($like): void {
                $w->where('t_usuarios.usuario', 'like', $like)
                    ->orWhere('t_usuarios.ciudad', 'like', $like)
                    ->orWhere('t_persona.nombre', 'like', $like)
                    ->orWhere('t_persona.paterno', 'like', $like)
                    ->orWhere('t_persona.materno', 'like', $like)
                    ->orWhere('t_persona.correo', 'like', $like)
                    ->orWhere('t_persona.celular', 'like', $like)
                    ->orWhere('t_clientes.razon_social', 'like', $like)
                    ->orWhere('t_proveedores.nombre_comercial', 'like', $like);
            });
        }

        $this->applyUsuarioSort($query, $sort, $dir);

        $usuarios = $query
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        $actor = $this->authed();
        $wantsCrear = $request->query('open_modal') === 'crear' || (string) $request->session()->get('open_modal') === 'crear';
        $wantsEditar = $request->query('open_modal') === 'editar' || (string) $request->session()->get('open_modal') === 'editar';
        $editId = (int) (session('edit_usuario_id') ?: $request->query('edit_usuario', 0));
        if ($editId === 0) {
            $editId = (int) old('editing_user_id', 0);
        }

        $editUsuario = null;
        if ($wantsEditar && $editId > 0) {
            $candidato = Usuario::query()
                ->with(['persona', 'rol', 'cliente', 'proveedor'])
                ->find($editId);
            if ($candidato !== null) {
                Gate::forUser($actor)->authorize('update', $candidato);
                $editUsuario = $candidato;
            }
        }

        return view('panel.consultor.usuarios.index', array_merge(
            $this->formCatalogos(),
            [
                'usuarios' => $usuarios,
                'perPage' => $perPage,
                'q' => $q,
                'sort' => $sort,
                'dir' => $dir,
                'autoshowModalCrear' => $wantsCrear && Gate::forUser($actor)->allows('create', Usuario::class),
                'autoshowModalEditar' => $wantsEditar && $editUsuario !== null,
                'editUsuario' => $editUsuario,
            ],
        ));
    }

    public function toggleActivo(Usuario $usuario): RedirectResponse
    {
        $this->authorize('toggleActivo', $usuario);
        $this->usuarios->setActivo($usuario, ! $usuario->isActive());

        return back()->with('status', 'Estado del usuario actualizado.');
    }

    private function applyUsuarioSort(Builder $query, string $sort, string $dir): void
    {
        $map = [
            'id_usuario' => 't_usuarios.id_usuario',
            'usuario' => 't_usuarios.usuario',
            'id_rol' => 't_usuarios.id_rol',
            'ciudad' => 't_usuarios.ciudad',
            'activo' => 't_usuarios.activo',
            'correo' => 't_persona.correo',
            'celular' => 't_persona.celular',
            'cliente' => 't_clientes.razon_social',
        ];
        if (isset($map[$sort])) {
            $query->orderBy($map[$sort], $dir);

            return;
        }
        if ($sort === 'nombre') {
            $query->orderBy('t_persona.nombre', $dir)
                ->orderBy('t_persona.paterno', $dir)
                ->orderBy('t_persona.materno', $dir);

            return;
        }
        $query->orderBy('t_usuarios.id_usuario', 'asc');
    }

    public function create(Request $request): RedirectResponse
    {
        $this->authorize('create', Usuario::class);

        return redirect()->route('panel.consultor.usuarios.index', array_merge(
            $this->onlyIndexQuery($request),
            ['open_modal' => 'crear'],
        ));
    }

    public function store(StoreUsuarioGestionRequest $request): RedirectResponse
    {
        $this->authorize('create', Usuario::class);
        $actor = $this->authed();
        Gate::forUser($actor)->authorize('assignRoleOnCreate', (int) $request->input('id_rol'));

        $data = $this->payloadCrearOActualizar($request, true);
        $data['creado_por'] = (int) $actor->id_usuario;
        $this->usuarios->crear($data);

        return redirect()
            ->route('panel.consultor.usuarios.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(Request $request, Usuario $usuario): RedirectResponse
    {
        $this->authorize('update', $usuario);

        return redirect()->route('panel.consultor.usuarios.index', array_merge(
            $this->onlyIndexQuery($request),
            [
                'open_modal' => 'editar',
                'edit_usuario' => $usuario->id_usuario,
            ],
        ));
    }

    public function update(UpdateUsuarioGestionRequest $request, Usuario $usuario): RedirectResponse
    {
        $this->authorize('update', $usuario);
        $actor = $this->authed();
        Gate::forUser($actor)->authorize('assignRoleOnUpdate', [$usuario, (int) $request->input('id_rol')]);

        $data = $this->payloadCrearOActualizar($request, false);
        $this->usuarios->actualizar($usuario, $data);

        return redirect()
            ->route('panel.consultor.usuarios.index')
            ->with('status', 'Usuario actualizado correctamente.');
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
     * @return array{roles: \Illuminate\Database\Eloquent\Collection, clientes: \Illuminate\Database\Eloquent\Collection, proveedores: \Illuminate\Database\Eloquent\Collection}
     */
    private function formCatalogos(): array
    {
        return [
            'roles' => CatRol::query()->orderBy('id_rol')->get(),
            'clientes' => Cliente::query()->orderBy('razon_social')->get(),
            'proveedores' => Proveedor::query()->orderBy('razon_social_proveedor')->get(),
        ];
    }

    private function payloadCrearOActualizar(StoreUsuarioGestionRequest|UpdateUsuarioGestionRequest $request, bool $isCreate): array
    {
        $b = $request->safe()->all();
        $permisos = [
            'permiso_ver_documentos' => (bool) ($b['permiso_ver_documentos'] ?? false),
            'permiso_subir_documentos' => (bool) ($b['permiso_subir_documentos'] ?? false),
            'permiso_crear_solicitudes' => (bool) ($b['permiso_crear_solicitudes'] ?? false),
        ];
        if ($isCreate) {
            return array_merge([
                'paterno' => $b['paterno'],
                'materno' => $b['materno'] ?? '',
                'nombre' => $b['nombre'],
                'telefono' => $b['telefono'],
                'celular' => $b['celular'],
                'correo' => $b['correo'],
                'direccion' => $b['direccion'] ?? '',
                'identificacion' => $b['identificacion'] ?? null,
                'usuario' => $b['usuario'],
                'password' => $b['password'],
                'id_rol' => (int) $b['id_rol'],
                'ciudad' => $b['ciudad'],
                'id_cliente' => isset($b['id_cliente']) ? (int) $b['id_cliente'] : null,
                'id_proveedor' => isset($b['id_proveedor']) ? (int) $b['id_proveedor'] : null,
            ], $permisos);
        }

        $row = array_merge([
            'paterno' => $b['paterno'],
            'materno' => $b['materno'] ?? '',
            'nombre' => $b['nombre'],
            'telefono' => $b['telefono'],
            'celular' => $b['celular'],
            'correo' => $b['correo'],
            'direccion' => $b['direccion'] ?? '',
            'identificacion' => $b['identificacion'] ?? null,
            'usuario' => $b['usuario'],
            'id_rol' => (int) $b['id_rol'],
            'ciudad' => $b['ciudad'],
            'id_cliente' => isset($b['id_cliente']) ? (int) $b['id_cliente'] : null,
            'id_proveedor' => isset($b['id_proveedor']) ? (int) $b['id_proveedor'] : null,
        ], $permisos);
        if (! empty($b['password'])) {
            $row['password'] = $b['password'];
        }

        return $row;
    }
}
