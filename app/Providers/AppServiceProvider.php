<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Middlewares\RoleMiddleware as Role;
use App\Models\MovimientoInventario;
use App\Observers\MovimientoInventarioObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('role', function ($app) {
            return new Role();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        MovimientoInventario::observe(MovimientoInventarioObserver::class);
    }
}
