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

        // 6M Filter မှာ ပေါ်မည့် data (လွန်ခဲ့သော ၅ လ)
        \App\Models\Shop::factory(10)->create([
            'created_at' => now()->subMonths(5),
        ]);

        // 9M Filter မှာ ပေါ်မည့် data (လွန်ခဲ့သော ၈ လ)
        \App\Models\Shop::factory(10)->create([
            'created_at' => now()->subMonths(8),
        ]);

        // All မှာပဲ ပေါ်မည့် data (လွန်ခဲ့သော ၁ နှစ်ကျော်)
        \App\Models\Shop::factory(10)->create([
            'created_at' => now()->subMonths(14),
        ]);
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
        ]);
    }
}
