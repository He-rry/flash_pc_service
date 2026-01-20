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
            // သင်ပေးထားတဲ့ ကိုဩဒိနိတ် Range အတွင်းမှာပဲ Random ချမယ်
            'lat' => fake()->latitude(16.8080, 16.8583),
            'lng' => fake()->longitude(96.1333, 96.1522),
            'address' => fake()->address(),
        ];
    }
}
