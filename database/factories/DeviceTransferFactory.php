<?php

namespace Database\Factories;

use App\Enums\Device\DeviceTransferStatus;
use App\Models\DeviceTransfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceTransfer>
 */
class DeviceTransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sourceUser = UserFactory::new()->create();
        $targetUser = UserFactory::new()->create();
        $device = DeviceFactory::new()->for($sourceUser)->create();

        return [
            'device_id' => $device->id,
            'source_user_id' => $sourceUser->id,
            'target_user_id' => $targetUser->id,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (DeviceTransfer $transfer) {
            $transfer->device
                ->update(['user_id' => $transfer->source_user_id]);
        });
    }

    /**
     * Create a device transfer with 'accepted' status.
     */
    public function accepted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => DeviceTransferStatus::ACCEPTED,
            ];
        })->afterCreating(function (DeviceTransfer $transfer) {
            $transfer->device
                ->update(['user_id' => $transfer->target_user_id]);
        });
    }

    /**
     * Create a device transfer with 'canceled' status.
     */
    public function canceled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => DeviceTransferStatus::CANCELED,
            ];
        });
    }

    /**
     * Create a device transfer with 'rejected' status.
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => DeviceTransferStatus::REJECTED,
            ];
        });
    }
}
