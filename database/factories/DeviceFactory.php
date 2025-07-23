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
            'device_model_id' => DeviceModelFactory::new(),
            'color' => fake()->randomElement($colors),
            'imei_1' => self::generateRandomNumber(15),
            'imei_2' => self::generateRandomNumber(15),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($device) {
            InvoiceFactory::new()->for($device)->create();
        });
    }
}
