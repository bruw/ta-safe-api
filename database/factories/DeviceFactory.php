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
        $description = $rejected
            ? $this->invalidProductDescription($device)
            : $this->validProductDescription($device);

        $device->invoice->update([
            'consumer_name' => $device->user->name,
            'consumer_cpf' => $device->user->cpf,
            'product_description' => $description,
        ]);
    }

    /**
     * Returns a randomly generated invalid description.
     */
    private function invalidProductDescription(Device $device): string
    {
        return implode('', $this->createInvoiceProducts($device));
    }

    /**
     * Returns a valid description for the given device.
     */
    private function validProductDescription(Device $device): string
    {
        $product = ['<span>'
            . " Smartphone {$device->deviceModel->brand->name}"
            . " {$device->deviceModel->name}"
            . " {$device->deviceModel->ram}"
            . " {$device->deviceModel->storage}"
            . " {$device->color}"
            . ' </span>',
        ];

        return implode('', $this->createInvoiceProducts($device, $product));
    }

    private function createInvoiceProducts(?Device $device, array $product = []): array
    {
        $deviceModel = $device->deviceModel;

        return array_merge([
            '<span> Placa de Vídeo ASUS TUF RX 9060 XT OC 16G Gaming AMD Radeon, 16GB, GDDR6, 128bits, RGB </span>',
        ], $product);
    }
}
