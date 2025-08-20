<?php

namespace Database\Factories;

use App\Traits\RandomNumberGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceSharingToken>
 */
class DeviceSharingTokenFactory extends Factory
{
    use RandomNumberGenerator;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'device_id' => DeviceFactory::new()->for(UserFactory::new()),
            'token' => $this->generateRandomNumber(8),
            'expires_at' => now()->addDay(),
        ];
    }

    /**
     * Create a device sharing token that has expired.
     */
    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            return ['expires_at' => now()->subMinute()];
        });
    }
}
