<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Uso: ->middleware('role:2,3') (ids de t_cat_roles).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roleIds): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $expected = array_map('intval', $roleIds);
        $actual = (int) $user->id_rol;

        if (in_array($actual, $expected, true)) {
            return $next($request);
        }

        return redirect()->to('/')->with('error', 'No tiene permisos para acceder a esta sección.');
    }
}
