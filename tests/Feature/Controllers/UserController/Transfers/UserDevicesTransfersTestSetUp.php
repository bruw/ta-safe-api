<?php

namespace Tests\Feature\Controllers\UserController\Transfers;

use App\Models\DeviceTransfer;
use App\Models\User;
use Database\Factories\DeviceTransferFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class UserDevicesTransfersTestSetUp extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;

    protected User $user;
    protected User $targetUser;
    protected DeviceTransfer $transfer;

    protected function setUp(): void
    {
        parent::SetUp();
        $this->userSetUp();
        $this->transferSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
        $this->targetUser = UserFactory::new()->create();
    }

    private function transferSetUp(): void
    {
        $this->transfer = DeviceTransferFactory::new()->create([
            'source_user_id' => $this->user->id,
            'target_user_id' => $this->targetUser->id,
        ]);
    }

    protected function route(): string
    {
        return route('api.user.devices.transfers');
    }
}
