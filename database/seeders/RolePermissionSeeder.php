<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = [
            'manage-services',
            'view-services-info',
            'add-services',
            'edit-services',
            'delete-services',
            'add-status',
            'edit-status',
            'delete-status',
            'add-service-type',
            'edit-service-type',
            'delete-service-type',
            'view-shop-management',
            'shop-list',
            'shop-create',
            'shop-edit',
            'shop-delete',
            'shop-import',
            'shop-duplicate-download',
            'shop-export',
            'route-list',
            'route-create',
            'route-delete',
            'route-view',
            'manage-users',
            'user-delete',
            'view-logs',
            'view-services',
            'view-settings',
            'view-filters',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }
        Role::findOrCreate('super-admin')->syncPermissions(Permission::all());
        Role::findOrCreate('manager')->syncPermissions(
            [
                'manage-services',
                'view-services-info',
                'view-shop-management',
                'shop-list',
                'route-list',
                'view-services',
                'view-settings',
                'shop-export',
                'shop-import'
            ]
        );
        Role::findOrCreate('editor')->syncPermissions([
            'manage-services',
            'shop-list',
            'shop-edit',
            'shop-import',
            'shop-duplicate-download',
            'view-shop-management',
            'view-services',
            'view-settings',
            'edit-services',
            'edit-status',
            'edit-service-type',
            'view-filters',
            'shop-import',
        ]);
        Role::findOrCreate('route-planner')->syncPermissions([
            'route-list',
            'route-create',
            'route-view'
        ]);
        Role::findOrCreate('log-manager')->syncPermissions([
            'view-logs',
            'view-shop-management',
            'shop-list',
            'view-settings'
        ]);
    }
}
