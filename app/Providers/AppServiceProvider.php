<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Solicitud;
use App\Models\SolicitudUsuario;
use App\Models\Usuario;
use App\Policies\ClientePolicy;
use App\Policies\ProveedorPolicy;
use App\Policies\SolicitudPolicy;
use App\Policies\SolicitudUsuarioPolicy;
use App\Policies\UsuarioPolicy;
use App\Repositories\Contracts\SolicitudRepository;
use App\Repositories\Eloquent\EloquentSolicitudRepository;
use App\Services\Panel\NotificacionConsultorService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SolicitudRepository::class, EloquentSolicitudRepository::class);
    }

    public function boot(): void
    {
        Gate::policy(Solicitud::class, SolicitudPolicy::class);
        Gate::policy(Cliente::class, ClientePolicy::class);
        Gate::policy(Proveedor::class, ProveedorPolicy::class);
        Gate::policy(Usuario::class, UsuarioPolicy::class);
        Gate::policy(SolicitudUsuario::class, SolicitudUsuarioPolicy::class);
        Paginator::useBootstrapFive();

        View::composer('layouts.partials.navbar-consultor', function ($view): void {
            $pendUsu = SolicitudUsuario::query()->where('estado', 'Pendiente')->count();
            $nuevasSol = Solicitud::query()->where('activo', 1)->where('estado', 'Nuevo')->count();
            $notifSvc = app(NotificacionConsultorService::class);
            $u = auth()->user();
            $notifNuevas = 0;
            $notifLista = collect();
            if ($u instanceof Usuario && in_array((int) $u->id_rol, [2, 3], true)) {
                $notifNuevas = $notifSvc->contarNoLeidas($u);
                $notifLista = $notifSvc->listarParaUsuario($u);
            }
            $view->with('notificacionBadgeCount', $notifNuevas + $pendUsu + $nuevasSol);
            $view->with('notificacionesConsultor', $notifLista);
        });
    }
}
