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
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UsuariosController extends Controller
{
    public function __construct(
        private readonly UsuarioGestionService $usuarios,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Usuario::class);

        $usuarios = Usuario::query()
            ->with(['rol', 'cliente', 'proveedor'])
            ->orderBy('id_usuario')
            ->paginate(30)
            ->withQueryString();

        return view('panel.consultor.usuarios.index', ['usuarios' => $usuarios]);
    }

    public function create(): View
    {
        $this->authorize('create', Usuario::class);

        return view('panel.consultor.usuarios.create', $this->formCatalogos());
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

    public function edit(Usuario $usuario): View
    {
        $this->authorize('update', $usuario);
        $usuario->load(['persona', 'rol', 'cliente', 'proveedor']);

        return view('panel.consultor.usuarios.edit', array_merge(
            $this->formCatalogos(),
            ['u' => $usuario],
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
