<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        if(\App::environment('production')) {
            \URL::forceScheme('https');
            \URL::forceRootUrl(config('app.url'));
        }
    }
}