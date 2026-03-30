<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // O Suer Adimn tem passe livre no sitema inteiro
        Gate::before(function ($user, $ability) {
            return $user->hasrole('Admin') ? true : null;
        });
    }
}
