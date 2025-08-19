<?php

namespace Tests\Unit\Actions\DeviceTransfer\Reject;

use App\Enums\Device\DeviceTransferStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\DeviceTransfer;
use Database\Factories\DeviceTransferFactory;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RejectDeviceTransferActionTest extends RejectDeviceTransferActionTestSetUp
{
    public function test_should_return_an_instance_of_device_transfer(): void
    {
        $result = $this->targetUser->deviceTransferService()->reject($this->transfer);
        $this->assertInstanceOf(DeviceTransfer::class, $result);
    }

    public function test_should_update_the_transfer_status_to_rejected(): void
    {
        $this->targetUser->deviceTransferService()->reject($this->transfer);

        $this->assertDatabaseHas('device_transfers', [
            'id' => $this->transfer->id,
            'status' => DeviceTransferStatus::REJECTED,
        ]);
    }

    public function test_should_throw_an_exception_when_the_user_is_not_the_target_user(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.transfer.not_target_user'));

        $this->sourceUser->deviceTransferService()->reject($this->transfer);
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

        $this->targetUser->deviceTransferService()->reject($transfer);
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

        $this->targetUser->deviceTransferService()->reject($transfer);
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

        $this->targetUser->deviceTransferService()->reject($transfer);
    }

    public function test_should_thrown_an_exception_when_occurred_an_internal_error(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.device_transfer.errors.reject'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->targetUser->deviceTransferService()->reject($this->transfer);
    }
}
