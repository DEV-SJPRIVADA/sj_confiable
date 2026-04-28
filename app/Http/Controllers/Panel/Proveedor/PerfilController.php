<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Proveedor;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePerfilPropioRequest;
use App\Models\Persona;
use App\Models\Usuario;
use App\Services\Panel\PerfilPropioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function show(Request $request): View
    {
        $usuario = $this->resolveUserWithPersona($request);
        $persona = $usuario->persona;
        if (! $persona instanceof Persona) {
            abort(404, 'Registro de persona no encontrado.');
        }

        return view('panel.consultor.perfil.show', [
            'usuario' => $usuario,
            'persona' => $persona,
            'perfilUpdateRoute' => 'panel.proveedor.perfil.update',
            'perfilTitle' => 'Mi Perfil — Proveedor',
        ]);
    }

    public function update(UpdatePerfilPropioRequest $request, PerfilPropioService $perfilPropio): RedirectResponse
    {
        $usuario = $this->resolveUserWithPersona($request);
        $persona = $usuario->persona;
        if (! $persona instanceof Persona) {
            abort(404, 'Registro de persona no encontrado.');
        }

        $perfilPropio->update($persona, $request->validated());

        return redirect()
            ->route('panel.proveedor.perfil.show')
            ->with('status', 'Datos personales actualizados correctamente.');
    }

    private function resolveUserWithPersona(Request $request): Usuario
    {
        $u = $request->user();
        if (! $u instanceof Usuario) {
            abort(403);
        }
        $u->load('persona');

        return $u;
    }
}
