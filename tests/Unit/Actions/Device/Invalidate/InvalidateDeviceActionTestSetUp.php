<?php

namespace Tests\Unit\Actions\Device\Invalidate;

use App\Models\Device;
use App\Models\User;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvalidateDeviceActionTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Device $device;

    protected function setUp(): void
    {
        parent::setUp();

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
}
