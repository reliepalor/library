<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        // Register your components (no need for Dropdown class)
        Blade::component('components.dropdown', 'dropdown');
        // Add other components as needed
        // View::addNamespace('mail', resource_path('views/emails'));
    }
}