<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register repository bindings in a dedicated provider
        $this->app->register(RepositoryServiceProvider::class);
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);

        // Gate to get super admin to all
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
