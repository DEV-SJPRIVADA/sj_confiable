<?php

namespace App\Providers;

use App\Models\Solicitud;
use App\Policies\SolicitudPolicy;
use App\Repositories\Contracts\SolicitudRepository;
use App\Repositories\Eloquent\EloquentSolicitudRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
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
        Paginator::useBootstrapFive();
    }
}
