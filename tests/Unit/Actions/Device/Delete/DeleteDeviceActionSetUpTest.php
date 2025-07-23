<?php

namespace Tests\Unit\Actions\Device\Delete;

use App\Models\Device;
use App\Models\User;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteDeviceActionSetUpTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Device $devicePending;
    protected Device $deviceValidated;
    protected Device $deviceInAnalysis;
    protected Device $deviceRejected;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();

        $this->devicePending = DeviceFactory::new()
            ->for($this->user)
            ->create();

        $this->deviceValidated = DeviceFactory::new()
            ->for($this->user)
            ->validated()
            ->create();

        $this->deviceInAnalysis = DeviceFactory::new()
            ->for($this->user)
            ->inAnalysis()
            ->create();

        $this->deviceRejected = DeviceFactory::new()
            ->for($this->user)
            ->rejected()
            ->create();
    }
}
