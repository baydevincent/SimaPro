<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        Paginator::useBootstrap();

        // Force HTTPS if accessed via ngrok or in production environment
        if (strpos($_SERVER['HTTP_HOST'] ?? '', 'ngrok') !== false || $this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
