<?php

namespace Tests\Unit\Actions\DeviceTransfer\Create;

use App\Models\Device;
use App\Models\User;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDeviceTransferActionTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $sourceUser;
    protected User $targetUser;
    protected Device $device;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->userSetUp();
        $this->deviceSetUp();
    }

    private function userSetUp(): void
    {
        $this->sourceUser = UserFactory::new()->create();
        $this->targetUser = UserFactory::new()->create();
    }

    private function deviceSetUp(): void
    {
        $this->device = DeviceFactory::new()
            ->for($this->sourceUser)
            ->validated()
            ->create();
    }
}
