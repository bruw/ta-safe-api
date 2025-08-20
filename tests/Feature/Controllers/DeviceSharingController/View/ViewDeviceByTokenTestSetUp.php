<?php

namespace Tests\Feature\Controllers\DeviceSharingController\View;

use App\Models\Device;
use App\Models\DeviceSharingToken;
use App\Models\User;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceSharingTokenFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class ViewDeviceByTokenTestSetUp extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;

    protected User $user;
    protected Device $device;
    protected DeviceSharingToken $deviceSharingToken;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->userSetUp();
        $this->deviceSetUp();
        $this->tokenSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

    private function deviceSetUp(): void
    {
        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->validated()
            ->create();
    }

    private function tokenSetUp(): void
    {
        $this->deviceSharingToken = DeviceSharingTokenFactory::new()
            ->for($this->device)
            ->create();
    }

    protected function route(?string $token = null): string
    {
        return route('api.device.share.view', ['token' => $token]);
    }
}
