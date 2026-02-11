<?php

namespace App\Providers;

use App\Repositories\RouteRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\ServiceTypeRepository;
use App\Repositories\StatusRepository;
use App\Repositories\ShopRepository;
use App\Repositories\RoutePlannerRepository;
use App\Interfaces\ServiceInterface;
use App\Interfaces\RouterInterface;
use App\Interfaces\ServiceTypeInterface;
use App\Interfaces\StatusRepositoryInterface;
use App\Interfaces\ShopRepositoryInterface;
use App\Interfaces\RoutePlannerRepositoryInterface;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ServiceInterface::class,
            ServiceRepository::class
        );

        $this->app->bind(
            RouterInterface::class,
            RouteRepository::class
        );

        $this->app->bind(
            ServiceTypeInterface::class,
            ServiceTypeRepository::class
        );

        $this->app->bind(
            StatusRepositoryInterface::class,
            StatusRepository::class
        );

        $this->app->bind(
            ShopRepositoryInterface::class,
            ShopRepository::class
        );

        $this->app->bind(
            RoutePlannerRepositoryInterface::class,
            RoutePlannerRepository::class
        );
    }

    public function boot(): void
    {
        // No boot actions required for repository bindings
    }
}
