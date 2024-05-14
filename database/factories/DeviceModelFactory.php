<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceModel>
 */
class DeviceModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Iphone 11', 'Galaxy S22', 'Poco x5 Pro', 'Edge 30']),
            'ram' => fake()->randomElement(['4 GB', '6 GB', '8 GB']),
            'storage' => fake()->randomElement(['64 GB', '128 GB', '256 GB']),
        ];
    }
}
