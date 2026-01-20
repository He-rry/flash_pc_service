<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            \App\Interfaces\ServiceInterface::class,
            \App\Repositories\ServiceRepository::class
        );
        $this->app->bind(
            \App\Interfaces\RouterInterface::class,
            \App\Repositories\RouteRepository::class
        );
        
    }

    public function boot(): void
    {
        //
    }
}
