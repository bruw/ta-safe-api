<?php

namespace Tests\Unit\Actions\Device;

use App\Enums\Device\DeviceTransferStatus;
use App\Enums\Device\DeviceValidationStatus;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\User;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateTransferDeviceTest extends TestCase
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
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED
            ]);
    }

    public function test_should_return_true_if_the_device_transfer_is_successfully_created(): void
    {
        $this->assertTrue(
            $this->sourceUser->transferDevice($this->targetUser, $this->device)
        );
    }

    public function test_when_creating_a_device_transfer_the_status_should_be_pending(): void
    {
        $this->assertTrue(
            $this->sourceUser->transferDevice($this->targetUser, $this->device)
        );

        $this->device->refresh();

        $this->assertEquals(
            $this->device->lastTransfer()->status,
            DeviceTransferStatus::PENDING
        );

        $this->assertEquals($this->sourceUser->devicesTransfers()->count(), 1);
        $this->assertEquals($this->targetUser->devicesTransfers()->count(), 1);
    }

    public function test_should_thrown_an_exception_when_the_user_does_not_have_permission_to_create_the_device_transfer(): void
    {
        $exceptionOcurred = false;

        try {
            $this->targetUser->transferDevice($this->sourceUser, $this->device);
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_FORBIDDEN
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('auth.unauthorized')
            );
        }

        $this->assertTrue($exceptionOcurred);

        $this->assertEquals($this->sourceUser->devicesTransfers()->count(), 0);
        $this->assertEquals($this->targetUser->devicesTransfers()->count(), 0);
    }

    public function test_should_thrown_an_exception_when_the_user_tries_to_transfer_a_device_to_yourself(): void
    {
        $exceptionOcurred = false;

        try {
            $this->sourceUser->transferDevice($this->sourceUser, $this->device);
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_transfer.not_yourself')
            );
        }

        $this->assertTrue($exceptionOcurred);

        $this->assertEquals($this->sourceUser->devicesTransfers()->count(), 0);
        $this->assertEquals($this->targetUser->devicesTransfers()->count(), 0);
    }

    public function test_should_thrown_an_exception_if_the_device_already_has_a_transfer_in_progress(): void
    {
        $exceptionOcurred = false;

        $this->sourceUser->transferDevice($this->targetUser, $this->device);

        try {
            $this->sourceUser->transferDevice($this->targetUser, $this->device);
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_transfer.in_progress')
            );
        }

        $this->assertTrue($exceptionOcurred);

        $this->assertEquals($this->sourceUser->devicesTransfers()->count(), 1);
        $this->assertEquals($this->targetUser->devicesTransfers()->count(), 1);
    }

    public function test_should_thrown_an_exception_if_the_devices_does_not_have_validated_status(): void
    {
        $invalidsStatus = [
            DeviceValidationStatus::PENDING,
            DeviceValidationStatus::REJECTED
        ];

        foreach ($invalidsStatus as $status) {
            $this->device->update([
                'validation_status' => $status
            ]);

            $exceptionOcurred = false;

            try {
                $this->sourceUser->transferDevice($this->targetUser, $this->device);
            } catch (Exception $e) {
                $exceptionOcurred = true;

                $this->assertEquals(
                    $e->getCode(),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );

                $this->assertEquals(
                    $e->getMessage(),
                    trans('validation.custom.device_transfer.register_not_validated')
                );
            }

            $this->assertTrue($exceptionOcurred);

            $this->assertEquals($this->sourceUser->devicesTransfers()->count(), 0);
            $this->assertEquals($this->targetUser->devicesTransfers()->count(), 0);
        }
    }
}
