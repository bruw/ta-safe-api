<?php

namespace Tests\Unit\Actions\DeviceTransfer\Reject;

use App\Models\DeviceTransfer;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RejectDeviceTransferActionTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $sourceUser;
    protected User $targetUser;
    protected DeviceTransfer $transfer;

    protected function setUp(): void
    {
        parent::setUp();

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
        $this->transfer = DeviceTransfer::factory()->create([
            'source_user_id' => $this->sourceUser->id,
            'target_user_id' => $this->targetUser->id,
        ]);
    }
}
