<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerGates();
    }

    protected function registerGates(): void
    {
        // Super Admin: full access
        Gate::before(function ($user, $ability) {
            if ($user->role === \App\Models\User::ROLE_SUPER_ADMIN) {
                return true;
            }
        });

        // manage-shops: add/update shops, import/export (Manager + Super Admin)
        Gate::define('manage-shops', function ($user) {
            return in_array($user->role, [
                \App\Models\User::ROLE_SUPER_ADMIN,
                \App\Models\User::ROLE_MANAGER,
            ], true);
        });

        // delete-shops: only Manager + Super Admin (same as manage-shops; separate if you want Super Admin only)
        Gate::define('delete-shops', function ($user) {
            return in_array($user->role, [
                \App\Models\User::ROLE_SUPER_ADMIN,
                \App\Models\User::ROLE_MANAGER,
            ], true);
        });

        // manage-routes: create/delete map routes (Manager + Super Admin)
        Gate::define('manage-routes', function ($user) {
            return in_array($user->role, [
                \App\Models\User::ROLE_SUPER_ADMIN,
                \App\Models\User::ROLE_MANAGER,
            ], true);
        });

        // view-logs: Activity Logs page and per-shop logs (Log Manager + Super Admin)
        Gate::define('view-logs', function ($user) {
            return in_array($user->role, [
                \App\Models\User::ROLE_SUPER_ADMIN,
                \App\Models\User::ROLE_LOG_MANAGER,
            ], true);
        });

        // manage-services: Services, Statuses, Service Types CRUD (Manager + Super Admin)
        Gate::define('manage-services', function ($user) {
            return in_array($user->role, [
                \App\Models\User::ROLE_SUPER_ADMIN,
                \App\Models\User::ROLE_MANAGER,
            ], true);
        });
    }
}
