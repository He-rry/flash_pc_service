<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory
{
    protected $model = Shop::class;
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' - PC Store',
            'lat' => fake()->latitude(16.8080, 16.8583),
            'lng' => fake()->longitude(96.1333, 96.1522),
            'address' => fake()->address(),
            'region' => fake()->randomElement(['Hlaing', 'Bahan']),
            'created_at' => fake()->dateTimeBetween('-12 months', 'now'),
        ];
    }
}
