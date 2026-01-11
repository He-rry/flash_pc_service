<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Status;
use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->numerify('09#########'),
            'customer_address' => $this->faker->address(),
            'customer_email' => $this->faker->safeEmail(),
            'lat' => $this->faker->latitude($min = 13.8, $max = 21.9),
            'long' => $this->faker->longitude($min = 94.5, $max = 98.2),
            'pc_model' => $this->faker->randomElement(['Dell Vostro', 'HP Pavilion', 'MacBook Air', 'Lenovo ThinkPad']),
            'issue_description' => $this->faker->paragraph(),
            'service_type_id' => ServiceType::inRandomOrder()->first()->id ?? 1,
            'status_id' => Status::inRandomOrder()->first()->id ?? 1,
        ];
    }
}
