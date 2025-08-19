<?php

namespace Tests\Unit\Actions\DeviceTransfer\Accept;

use App\Enums\Device\DeviceTransferStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\DeviceTransfer;
use Database\Factories\DeviceTransferFactory;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AcceptDeviceTransferActionTest extends AcceptDeviceTransferActionTestSetUp
{
    public function test_should_return_an_instance_of_device_transfer(): void
    {
        $result = $this->targetUser->deviceTransferService()->accept($this->deviceTransfer);
        $this->assertInstanceOf(DeviceTransfer::class, $result);
    }

    public function test_should_update_the_device_transfer_status_to_accepted(): void
    {
        $this->targetUser->deviceTransferService()->accept($this->deviceTransfer);

        $this->assertDatabaseHas('device_transfers', [
            'id' => $this->deviceTransfer->id,
            'status' => DeviceTransferStatus::ACCEPTED,
        ]);
    }

    public function test_should_update_the_device_owner_to_the_target_user(): void
    {
        $this->targetUser->deviceTransferService()->accept($this->deviceTransfer);

        $this->assertDatabaseHas('devices', [
            'id' => $this->deviceTransfer->device_id,
            'user_id' => $this->targetUser->id,
        ]);
    }

    public function test_should_thrown_an_exception_when_the_user_is_not_target(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.transfer.not_target_user'));

        $this->sourceUser->deviceTransferService()->accept($this->deviceTransfer);
    }

    public function test_should_thrown_an_exception_when_the_device_transfer_has_the_status_accepted(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.transfer.not_pending'));

        $transfer = DeviceTransferFactory::new()->accepted()->create([
            'source_user_id' => $this->sourceUser->id,
            'target_user_id' => $this->targetUser->id,
        ]);

        $this->targetUser->deviceTransferService()->accept($transfer);
    }

    public function test_should_thrown_an_exception_when_the_device_transfer_has_the_status_canceled(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.transfer.not_pending'));

        $transfer = DeviceTransferFactory::new()->canceled()->create([
            'source_user_id' => $this->sourceUser->id,
            'target_user_id' => $this->targetUser->id,
        ]);

        $this->targetUser->deviceTransferService()->accept($transfer);
    }

    public function test_should_thrown_an_exception_when_the_device_transfer_has_the_status_rejected(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.transfer.not_pending'));

        $transfer = DeviceTransferFactory::new()->rejected()->create([
            'source_user_id' => $this->sourceUser->id,
            'target_user_id' => $this->targetUser->id,
        ]);

        $this->targetUser->deviceTransferService()->accept($transfer);
    }

    public function test_should_thrown_an_exception_when_occurred_an_internal_error(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.device_transfer.errors.accept'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->targetUser->deviceTransferService()->accept($this->deviceTransfer);
    }
}
