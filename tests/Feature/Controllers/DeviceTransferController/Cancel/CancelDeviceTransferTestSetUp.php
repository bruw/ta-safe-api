<?php

namespace Tests\Feature\Controllers\DeviceTransferController\Cancel;

use App\Models\DeviceTransfer;
use App\Models\User;
use Database\Factories\DeviceTransferFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class CancelDeviceTransferTestSetUp extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;

    protected User $sourceUser;
    protected User $targetUser;
    protected DeviceTransfer $transfer;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->userSetUp();
        $this->deviceTransferSetUp();
    }

    private function userSetUp(): void
    {
        $this->sourceUser = UserFactory::new()->create();
        $this->targetUser = UserFactory::new()->create();
    }

    private function deviceTransferSetUp(): void
    {
        $this->transfer = DeviceTransferFactory::new()->create([
            'source_user_id' => $this->sourceUser->id,
            'target_user_id' => $this->targetUser->id,
        ]);
    }

    public function route(): string
    {
        return route('api.device.transfer.cancel', $this->transfer);
    }
}
