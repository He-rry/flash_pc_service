<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(MasterDataSeeder::class);
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
        \App\Models\User::create([
            'name' => 'System',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin'
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Admin1',
            'email' => 'admin1@test.com',
            'password' => bcrypt('password123'),
            'role' => 'manager'
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Admin2',
            'email' => 'admin2@test.com',
            'password' => bcrypt('password123'),
        ]);
    }
}
