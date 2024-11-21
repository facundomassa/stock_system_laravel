<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         // Variable para la operación seleccionada
        View::composer('*', function ($view) {
            $view->with('selected_operation', session('selected_operation'));
        });

        // Variable para las operaciones permitidas
        View::composer('*', function ($view) {
            $allowedOperations = auth()->check() ? auth()->user()->allowedOperations() : collect();
            $view->with('allowed_operations', session('allowed_operations') ?? $allowedOperations);
        });
    }
}
