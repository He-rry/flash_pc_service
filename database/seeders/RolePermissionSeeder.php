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
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ၁။ System တစ်ခုလုံးအတွက် လိုအပ်မယ့် Permission အစုံအလင်ကို အရင်ဆောက်မယ်
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
            'view-logs',
            'view-services',
            'view-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // ၂။ Role များ ဆောက်ပြီး Permission ပေးခြင်း
        
        // Super Admin
        Role::findOrCreate('super-admin')->givePermissionTo(Permission::all());

        // Manager
        Role::findOrCreate('manager')->givePermissionTo([
            'shop-list', 'route-list', 'view-services', 
            'view-settings', 'shop-export', 'shop-import'
        ]);

        // Editor
        Role::findOrCreate('editor')->givePermissionTo([
            'shop-list', 'shop-edit', 'shop-import', 'view-shop-management'
        ]);

        // Route Planner
        Role::findOrCreate('route-planner')->givePermissionTo([
            'route-list', 'route-create', 'route-view'
        ]);

        // Log Manager
        Role::findOrCreate('log-manager')->givePermissionTo(['view-logs']);


        // ၃။ ရှိပြီးသား User တွေကို Role တွေ အလိုအလျောက် သတ်မှတ်ပေးခြင်း (Mapping)
        $mapping = [
            User::ROLE_SUPER_ADMIN => 'super-admin',
            User::ROLE_MANAGER => 'manager',
            User::ROLE_EDITOR => 'editor',
            User::ROLE_LOG_MANAGER => 'log-manager',
        ];

        foreach (User::all() as $user) {
            $currentRoleColumn = $user->role; // သင့် table ထဲက column name ဖြစ်ရပါမယ်
            if ($currentRoleColumn && isset($mapping[$currentRoleColumn])) {
                $user->assignRole($mapping[$currentRoleColumn]);
            }
        }

        // Clear cache again
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}