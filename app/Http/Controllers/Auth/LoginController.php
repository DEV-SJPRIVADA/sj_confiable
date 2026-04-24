<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domain\Routing\RoleHome;
use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(
                RoleHome::pathFor(Auth::user())
            );
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'usuario' => ['required', 'string', 'max:245'],
            'password' => ['required', 'string', 'max:500'],
        ]);

        if (! Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password']], false)) {
            return back()
                ->withInput($request->only('usuario'))
                ->withErrors(['usuario' => 'Usuario o contraseña incorrectos.']);
        }

        /** @var Usuario $user */
        $user = Auth::user();
        if (! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('usuario'))
                ->withErrors(['usuario' => 'Usuario inactivo. Contacte a SJ Seguridad.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(RoleHome::pathFor($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
