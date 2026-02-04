<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Define permissions (keeps parity with existing Gates)
        $permissions = [
            'manage-shops', // add/update/import/export
            'delete-shops',
            'manage-routes',
            'view-logs',
            'manage-services',
            'import-shops',
            'export-shops',
            'manage-users'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $logManager = Role::firstOrCreate(['name' => 'log-manager']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());

        $manager->givePermissionTo([
            'manage-shops',
            'delete-shops',
            'manage-routes',
            'manage-services',
            'import-shops',
            'export-shops',
        ]);

        $editor->givePermissionTo([]); // view-only — rely on existing @can/view gates

        $logManager->givePermissionTo(['view-logs']);

        // Map existing users' `role` column to Spatie roles
        $mapping = [
            User::ROLE_SUPER_ADMIN => 'super-admin',
            User::ROLE_MANAGER => 'manager',
            User::ROLE_EDITOR => 'editor',
            User::ROLE_LOG_MANAGER => 'log-manager',
        ];

        foreach (User::all() as $user) {
            $current = $user->role;
            if ($current && isset($mapping[$current])) {
                $user->assignRole($mapping[$current]);
            }
        }

        // Clear cache again
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
