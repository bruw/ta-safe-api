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
            'name' => fake()->randomElement(['Iphone', 'Galaxy', 'Poco', 'Edge']),
            'ram' => fake()->randomElement(['4 GB, 6 GB, 8 GB']),
            'storage' => fake()->randomElement(['64 GB, 128 GB, 256 GB'])
        ];
    }
}
