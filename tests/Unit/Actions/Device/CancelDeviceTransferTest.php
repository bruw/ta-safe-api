<?php

namespace Tests\Unit\Actions\Device;

use App\Enums\Device\DeviceTransferStatus;
use App\Enums\Device\DeviceValidationStatus;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\DeviceTransfer;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CancelDeviceTransferTest extends TestCase
{
    use RefreshDatabase;

    private User $sourceUser;
    private User $targetUser;
    private DeviceTransfer $deviceTransfer;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->sourceUser = User::factory()->create();
        $this->targetUser = User::factory()->create();

        $deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $device = Device::factory()
            ->for($this->sourceUser)
            ->for($deviceModel)
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED,
            ]);

        $this->sourceUser->createDeviceTransfer(
            $this->targetUser,
            $device
        );

        $this->deviceTransfer = DeviceTransfer::where([
            'source_user_id' => $this->sourceUser->id,
        ])->firstOrFail();
    }

    public function test_should_return_true_when_a_pending_transfer_is_successfully_canceled(): void
    {
        $this->assertTrue(
            $this->sourceUser->cancelDeviceTransfer($this->deviceTransfer)
        );
    }

    public function test_must_update_the_transfer_status_to_canceled_and_not_change_the_device_owner(): void
    {
        $this->sourceUser->cancelDeviceTransfer($this->deviceTransfer);

        $this->assertEquals(
            $this->deviceTransfer->status,
            DeviceTransferStatus::CANCELED
        );

        $this->assertEquals(
            $this->deviceTransfer->device->user_id,
            $this->sourceUser->id
        );
    }

    public function test_should_return_an_exception_when_the_user_tries_to_cancel_a_finalized_transfer_that_has_an_accepted_status(): void
    {
        $occuredException = false;

        $this->deviceTransfer->update([
            'status' => DeviceTransferStatus::ACCEPTED,
        ]);

        try {
            $this->sourceUser->cancelDeviceTransfer($this->deviceTransfer);
        } catch (Exception $e) {
            $occuredException = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_transfer.transfer_closed')
            );
        }

        $this->deviceTransfer->refresh();

        $this->assertTrue($occuredException);

        $this->assertEquals(
            $this->deviceTransfer->device->user_id,
            $this->sourceUser->id
        );
    }

    public function test_should_return_an_exception_when_the_user_tries_to_cancel_a_finalized_transfer_that_has_an_canceled_status(): void
    {
        $occuredException = false;

        $this->deviceTransfer->update([
            'status' => DeviceTransferStatus::CANCELED,
        ]);

        try {
            $this->sourceUser->cancelDeviceTransfer($this->deviceTransfer);
        } catch (Exception $e) {
            $occuredException = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_transfer.transfer_closed')
            );
        }

        $this->deviceTransfer->refresh();

        $this->assertTrue($occuredException);

        $this->assertEquals(
            $this->deviceTransfer->device->user_id,
            $this->sourceUser->id
        );
    }

    public function test_should_return_an_exception_when_the_user_tries_to_cancel_a_finalized_transfer_that_has_an_rejected_status(): void
    {
        $occuredException = false;

        $this->deviceTransfer->update([
            'status' => DeviceTransferStatus::REJECTED,
        ]);

        try {
            $this->sourceUser->cancelDeviceTransfer($this->deviceTransfer);
        } catch (Exception $e) {
            $occuredException = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_transfer.transfer_closed')
            );
        }

        $this->deviceTransfer->refresh();

        $this->assertTrue($occuredException);

        $this->assertEquals(
            $this->deviceTransfer->device->user_id,
            $this->sourceUser->id
        );
    }
}
