<?php

namespace Tests\Unit\Models\User\Collections;

use App\Enums\Device\DeviceTransferStatus;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\DeviceTransfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDevicesTransfersTest extends TestCase
{
    use RefreshDatabase;

    private User $sourceUser;
    private User $targetUser;
    private Device $device;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->sourceUser = User::factory()->create();
        $this->targetUser = User::factory()->create();

        $deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->device = Device::factory()
            ->for($this->sourceUser)
            ->for($deviceModel)
            ->create();

        $this->device->refresh();
    }

    public function test_should_return_null_when_the_user_has_no_device_transfers(): void
    {
        $this->assertEmpty(
            $this->sourceUser->userDevicesTransfers()
        );

        $this->assertEmpty(
            $this->targetUser->userDevicesTransfers()
        );
    }

    public function test_should_return_the_user_device_transfers(): void
    {
        $firstTransfer = DeviceTransfer::create([
            'device_id' => $this->device->id,
            'source_user_id' => $this->sourceUser->id,
            'target_user_id' => $this->targetUser->id,
            'status' => DeviceTransferStatus::ACCEPTED,
        ]);

        $lastTransfer = DeviceTransfer::create([
            'device_id' => $this->device->id,
            'source_user_id' => $this->targetUser->id,
            'target_user_id' => $this->sourceUser->id,
            'status' => DeviceTransferStatus::PENDING,
        ]);

        $this->device->refresh();

        $transfers = [$firstTransfer, $lastTransfer];

        foreach ($transfers as $transfer) {
            $this->assertTrue(
                $this->sourceUser->userDevicesTransfers()->contains($transfer)
            );

            $this->assertEquals(
                $this->sourceUser->userDevicesTransfers()->count(),
                2
            );
        }

        foreach ($transfers as $transfer) {
            $this->assertTrue(
                $this->targetUser->userDevicesTransfers()->contains($transfer)
            );

            $this->assertEquals(
                $this->targetUser->userDevicesTransfers()->count(),
                2
            );
        }
    }
}
