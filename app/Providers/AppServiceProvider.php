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
use App\Services\Panel\NotificacionClienteService;
use App\Services\Panel\NotificacionConsultorService;
use App\Services\Panel\NotificacionProveedorService;
use App\Services\Solicitud\SolicitudDocumentoPathResolver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SolicitudRepository::class, EloquentSolicitudRepository::class);

        $this->app->singleton(SolicitudDocumentoPathResolver::class, static function (): SolicitudDocumentoPathResolver {
            $roots = config('sj.document_roots', []);
            /** @var list<string> $list */
            $list = array_values(array_filter(is_array($roots) ? $roots : []));

            return new SolicitudDocumentoPathResolver($list);
        });
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
            $notifSvc = app(NotificacionConsultorService::class);
            $u = auth()->user();
            $notifNuevas = 0;
            $notifLista = collect();
            if ($u instanceof Usuario && in_array((int) $u->id_rol, [2, 3], true)) {
                $notifNuevas = $notifSvc->contarNoLeidas($u);
                $notifLista = $notifSvc->listarParaUsuario($u);
            }
            // Badge del ícono: sólo alertas sin leer de `notificaciones` (antes sumaba Pendiente solicitudes-usuario + "Nuevo", confundiendo al marcar leídas).
            $view->with('notificacionBadgeCount', $notifNuevas);
            $view->with('notificacionesConsultor', $notifLista);
        });

        View::composer('layouts.partials.navbar-cliente', function ($view): void {
            $svc = app(NotificacionClienteService::class);
            /** @var \Illuminate\Contracts\Auth\Authenticatable|null $auth */
            $auth = auth()->user();
            $lista = collect();
            $count = 0;
            if ($auth instanceof Usuario && in_array((int) $auth->id_rol, [1, 4, 5], true)) {
                $count = $svc->contarNoLeidas($auth);
                $lista = $svc->listarParaUsuario($auth);
            }
            $view->with('clienteNotificacionBadge', $count);
            $view->with('notificacionesCliente', $lista);
        });

        View::composer('layouts.partials.navbar-proveedor', function ($view): void {
            $svc = app(NotificacionProveedorService::class);
            /** @var \Illuminate\Contracts\Auth\Authenticatable|null $auth */
            $auth = auth()->user();
            $lista = collect();
            $count = 0;
            if ($auth instanceof Usuario && (int) $auth->id_rol === 6 && $auth->id_proveedor !== null) {
                $count = $svc->contarNoLeidas($auth);
                $lista = $svc->listarParaUsuario($auth);
            }
            $view->with('proveedorNotificacionBadge', $count);
            $view->with('notificacionesProveedor', $lista);
        });
    }
}
