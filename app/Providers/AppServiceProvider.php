<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        // Register repository bindings in a dedicated provider
        $this->app->register(\App\Providers\RepositoryServiceProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
