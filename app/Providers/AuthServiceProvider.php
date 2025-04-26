<?php

namespace App\Providers;


use App\Models\Dish;
use App\Models\Combo;
use App\Models\RestaurantOrder;
use App\Models\Loss;
use App\Policies\DishPolicy;
use App\Policies\ComboPolicy;
use App\Policies\RestaurantOrderPolicy;
use App\Policies\LossPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Combo::class             => ComboPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('open-order', function ($user) {
            // Lógica para determinar si el usuario tiene permiso para abrir pedidos
            return $user->hasPermissionTo('open-order');
        });
    }
}
