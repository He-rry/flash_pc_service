<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Cache များကို ရှင်းထုတ်ခြင်း
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = [
            'view-services-info',
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
            'shop-export',
            'route-list',
            'route-create',
            'route-delete',
            'route-view',
            'manage-users',
            'user-delete', // New: For Restore/Force Delete
            'view-logs',
            'view-services',
            'view-settings',
            'view-filters',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }
        Role::findOrCreate('super-admin')->givePermissionTo(Permission::all());
        Role::findOrCreate('manager')->givePermissionTo([
            'shop-list', 'route-list', 'view-services', 
            'view-settings', 'shop-export', 'shop-import'
        ]);
        Role::findOrCreate('editor')->givePermissionTo([
            'shop-list', 'shop-edit', 'shop-import', 'view-shop-management'
        ]);
        Role::findOrCreate('route-planner')->givePermissionTo([
            'route-list', 'route-create', 'route-view'
        ]);
        Role::findOrCreate('log-manager')->givePermissionTo(['view-logs']);
    }
}