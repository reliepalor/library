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
        $this->app->singleton('image', function ($app) {
            return Intervention\Image\ImageManager::gd();
        });
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