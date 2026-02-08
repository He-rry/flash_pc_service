<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'log-manager']);
        $this->call([
            RolePermissionSeeder::class,
            MasterDataSeeder::class,
        ]);
        \App\Models\Service::factory(15)->create();
        \App\Models\Shop::factory(10)->create([
            'created_at' => now()->subMonths(2),
        ]);

        // 6M Filter
        \App\Models\Shop::factory(10)->create([
            'created_at' => now()->subMonths(5),
        ]);

        // 9M Filter
        \App\Models\Shop::factory(10)->create([
            'created_at' => now()->subMonths(8),
        ]);

        // All 
        \App\Models\Shop::factory(10)->create([
            'created_at' => now()->subMonths(14),
        ]);
        $superAdmin = \App\Models\User::create([
            'name' => 'System',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);
        $manager = \App\Models\User::factory()->create([
            'name' => 'Admin1',
            'email' => 'admin1@test.com',
            'password' => bcrypt('password123'),
            'role' => 'manager'
        ]);
        $logManager = \App\Models\User::factory()->create([
            'name' => 'Admin2',
            'email' => 'admin2@test.com',
            'password' => bcrypt('password123'),
        ]);
        $superAdmin->assignRole('super-admin');
        $manager->assignRole('manager');
        $logManager->assignRole('log-manager');
    }
}
