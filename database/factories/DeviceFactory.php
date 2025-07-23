<?php

namespace Database\Factories;

use App\Enums\Device\DeviceValidationStatus;
use App\Models\Device;
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
        return $this->afterCreating(function (Device $device) {
            InvoiceFactory::new()->for($device)->create();
        });
    }

    /**
     * Set the device validation status to validated and update the associated invoice.
     */
    public function validated(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'validation_status' => DeviceValidationStatus::VALIDATED,
            ];
        })->afterCreating(function (Device $device) {
            $this->updateInvoice($device);
        });
    }

    /**
     * Set the device validation status to in analysis and update the associated invoice.
     */
    public function inAnalysis(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'validation_status' => DeviceValidationStatus::IN_ANALYSIS,
            ];
        })->afterCreating(function (Device $device) {
            $this->updateInvoice($device);
        });
    }

    /**
     * Set the device validation status to rejected and update the associated invoice.
     */
    public function rejected(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'validation_status' => DeviceValidationStatus::REJECTED,
            ];
        })->afterCreating(function (Device $device) {
            $this->updateInvoice($device, true);
        });
    }

    /**
     * Updates the invoice associated with a given device.
     */
    private function updateInvoice(Device $device, bool $rejected = false): void
    {
        $model = $device->deviceModel;
        $desc = "Smartphone {$model->brand->name} {$model->name} {$model->ram} {$model->storage} {$device->color}";

        $device->invoice->update([
            'consumer_name' => $device->user->name,
            'consumer_cpf' => $device->user->cpf,
            'product_description' => $rejected ? fake()->text(255) : $desc,
        ]);
    }
}
