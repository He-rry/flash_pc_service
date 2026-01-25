<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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

        $this->app->bind(
            \App\Interfaces\ServiceTypeInterface::class,
            \App\Repositories\ServiceTypeRepository::class
        );

        $this->app->bind(
            \App\Interfaces\StatusRepositoryInterface::class,
            \App\Repositories\StatusRepository::class
        );

        $this->app->bind(
            \App\Interfaces\ShopRepositoryInterface::class,
            \App\Repositories\ShopRepository::class
        );

        $this->app->bind(
            \App\Interfaces\RoutePlannerRepositoryInterface::class,
            \App\Repositories\RoutePlannerRepository::class
        );
    }

    public function boot(): void
    {
        // No boot actions required for repository bindings
    }
}
