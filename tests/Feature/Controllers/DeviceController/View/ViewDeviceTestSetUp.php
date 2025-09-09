<?php

namespace Tests\Feature\Controllers\DeviceController\View;

use App\Models\Device;
use App\Models\User;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class ViewDeviceTestSetUp extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;

    protected User $user;
    protected Device $device;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->userSetUp();
        $this->deviceSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

    private function deviceSetUp(): void
    {
        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->create();
    }

    protected function route(): string
    {
        return route('api.device.view', $this->device);
    }
}
