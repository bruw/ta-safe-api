<?php

namespace Database\Factories;

use App\Traits\RandomNumberGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    use RandomNumberGenerator;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $colors = ['Preto', 'Branco', 'Azul'];
        
        return [
            'color' => fake()->randomElement($colors),
            'imei_1' => self::generateRandomNumber(15),
            'imei_2' => self::generateRandomNumber(15)
        ];
    }
}
