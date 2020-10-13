<?php

namespace App\Providers;

use Carbon\Carbon;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::enableImplicitGrant();

        Passport::tokensCan([
            'purchase-product' => 'Crear transacciones para comprar productos determinados',
            'manage-products' =>'Crear, ver, actualizar y eliminar productos',
            'manage-account' => 'Obtener la información de la cuenta, nombre, email, estado (sin contraseña), modificar datos como email, nombre y contraseña. no puede eliminar la cuenta',
            'read-general' => 'Obtener información general, categorias donde se compra y se vende, productos vendidos o comprados, transacciones, compras y ventas',
        ]);
    }
}
