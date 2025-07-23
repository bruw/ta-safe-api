<?php

namespace Tests\Feature\Controllers\DeviceController\Delete;

use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\User;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class DeleteDeviceSetUpTest extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;

    protected User $user;
    protected DeviceModel $deviceModel;
    protected Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();

        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->rejected()
            ->create();
    }

    protected function route(): string
    {
        return route('api.users.devices.delete', $this->device);
    }

    protected function successDeleteDevice(): array
    {
        return [
            'type' => 'success',
            'msg' => trans_choice('flash_messages.success.deleted.m', 1, [
                'model' => trans_choice('model.device', 1),
            ]),
        ];
    }
}
