<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\Status::insert([
            ['status_name' => 'New'],
            ['status_name' => 'On Going'],
            ['status_name' => 'Processing'],
            ['status_name' => 'Finished']
        ]);

        \App\Models\ServiceType::insert([
            ['service_name' => 'PC Installation'],
            ['service_name' => 'PC Repair'],
            ['service_name' => 'Software Installation'],
            ['service_name' => 'Laptop Repair'],
        ]);
    }
}
