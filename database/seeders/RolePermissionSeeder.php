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

        // ၁။ System တစ်ခုလုံးအတွက် Permission များ သတ်မှတ်ခြင်း
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

        // ၂။ Role များ ဆောက်ပြီး Permission ပေးခြင်း
        
        // Super Admin: Permission အားလုံး ပေးထားမည်
        Role::findOrCreate('super-admin')->givePermissionTo(Permission::all());

        // Manager: စီမံခန့်ခွဲမှုပိုင်းဆိုင်ရာ Permission များ
        Role::findOrCreate('manager')->givePermissionTo([
            'shop-list', 'route-list', 'view-services', 
            'view-settings', 'shop-export', 'shop-import'
        ]);

        // Editor: ဆိုင်အချက်အလက် ပြင်ဆင်နိုင်သူ
        Role::findOrCreate('editor')->givePermissionTo([
            'shop-list', 'shop-edit', 'shop-import', 'view-shop-management'
        ]);

        // Route Planner: မြေပုံနှင့် လမ်းကြောင်း ရေးဆွဲသူ
        Role::findOrCreate('route-planner')->givePermissionTo([
            'route-list', 'route-create', 'route-view'
        ]);

        // Log Manager: မှတ်တမ်းများ ကြည့်ရှုနိုင်သူ
        Role::findOrCreate('log-manager')->givePermissionTo(['view-logs']);


        // ၃။ ရှိပြီးသား User များကို Spatie Role စနစ်သို့ ပြောင်းလဲပေးခြင်း (Mapping)
        $mapping = [
            User::ROLE_SUPER_ADMIN => 'super-admin',
            User::ROLE_MANAGER => 'manager',
            User::ROLE_EDITOR => 'editor',
            User::ROLE_LOG_MANAGER => 'log-manager',
        ];

        foreach (User::all() as $user) {
            $currentRoleColumn = $user->role; // users table ထဲက role column name
            if ($currentRoleColumn && isset($mapping[$currentRoleColumn])) {
                $user->assignRole($mapping[$currentRoleColumn]);
            }
        }

        // အပြောင်းအလဲများပြီးနောက် Cache ကို ထပ်မံရှင်းထုတ်ခြင်း
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}