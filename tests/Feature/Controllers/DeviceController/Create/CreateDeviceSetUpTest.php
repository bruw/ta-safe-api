<?php

namespace Tests\Feature\Controllers\DeviceController\Create;

use App\Models\Device;
use App\Models\User;
use App\Traits\RandomNumberGenerator;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class CreateDeviceSetUpTest extends TestCase
{
    use AccessAsserts;
    use RandomNumberGenerator;
    use RefreshDatabase;

    protected User $user;
    protected Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
        $this->device = DeviceFactory::new()->for($this->user)->create();
    }

    protected function route(): string
    {
        return route('api.users.devices.create');
    }

    protected function successCreatedDevice(): array
    {
        return [
            'type' => 'success',
            'msg' => trans_choice('flash_messages.success.registered.m', 1, [
                'model' => trans_choice('model.device', 1),
            ]),
        ];
    }

    protected function validDeviceData(array $overrides = []): array
    {
        return array_merge([
            'device_model_id' => $this->device->deviceModel->id,
            'access_key' => $this->generateRandomNumber(44),
            'color' => 'white',
            'imei_1' => $this->generateRandomNumber(15),
            'imei_2' => $this->generateRandomNumber(15),
        ], $overrides);
    }
}
