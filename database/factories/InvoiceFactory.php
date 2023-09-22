<?php

namespace Database\Factories;

use App\Traits\RandomNumberGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
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
            'access_key' => self::generateRandomNumber(44)
        ];
    }
}
