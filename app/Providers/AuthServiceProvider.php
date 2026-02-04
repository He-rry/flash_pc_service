<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;


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
            if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
                return true;
            }
        });

        // manage-shops: add/update shops, import/export (Manager + Super Admin)
        Gate::define('manage-shops', function ($user) {
            return in_array($user->role, [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_MANAGER,
            ], true) || (method_exists($user, 'hasRole') && $user->hasRole('manager'));
        });

        // delete-shops: only Manager + Super Admin (same as manage-shops; separate if you want Super Admin only)
        Gate::define('delete-shops', function ($user) {
            return in_array($user->role, [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_MANAGER,
            ], true) || (method_exists($user, 'hasRole') && $user->hasRole('manager'));
        });

        // manage-routes: create/delete map routes (Manager + Super Admin)
        Gate::define('manage-routes', function ($user) {
            return in_array($user->role, [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_MANAGER,
            ], true) || (method_exists($user, 'hasRole') && $user->hasRole('manager'));
        });

        // view-logs: Activity Logs page and per-shop logs (Log Manager + Super Admin)
        Gate::define('view-logs', function ($user) {
            return in_array($user->role, [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_LOG_MANAGER,
            ], true) || (method_exists($user, 'hasRole') && $user->hasRole('log-manager'));
        });

        // manage-services: Services, Statuses, Service Types CRUD (Manager + Super Admin)
        Gate::define('manage-services', function ($user) {
            return in_array($user->role, [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_MANAGER,
            ], true) || (method_exists($user, 'hasRole') && $user->hasRole('manager'));
        });
    }
}
