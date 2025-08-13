<?php

namespace Tests\Unit\Actions\DeviceTransfer\Create;

use App\Enums\Device\DeviceTransferStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\DeviceTransfer;
use Database\Factories\DeviceFactory;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CreateDeviceTransferActionTest extends CreateDeviceTransferActionTestSetUp
{
    public function test_should_return_a_instance_of_the_device_transfer(): void
    {
        $transfer = $this->sourceUser
            ->deviceTransferService()
            ->create($this->targetUser, $this->device);

        $this->assertInstanceOf(DeviceTransfer::class, $transfer);
    }

    public function test_should_create_a_new_device_transfer_with_pending_status(): void
    {
        $transfer = $this->sourceUser
            ->deviceTransferService()
            ->create($this->targetUser, $this->device);

        $this->assertDatabaseHas('device_transfers', [
            'id' => $transfer->id,
            'device_id' => $this->device->id,
            'source_user_id' => $this->sourceUser->id,
            'target_user_id' => $this->targetUser->id,
            'status' => DeviceTransferStatus::PENDING,
        ]);
    }

    public function test_should_thrown_an_exception_when_the_source_user_is_not_owner_of_the_device(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);
        $this->expectExceptionMessage(trans('validators.device.user.owner'));

        $this->targetUser
            ->deviceTransferService()
            ->create($this->sourceUser, $this->device);
    }

    public function test_should_thrown_an_exception_when_trying_to_create_a_transfer_for_itself(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.transfer.same_user'));

        $this->sourceUser
            ->deviceTransferService()
            ->create($this->sourceUser, $this->device);
    }

    public function test_should_thrown_an_exception_when_the_device_status_is_pending(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.validated'));

        $device = DeviceFactory::new()
            ->for($this->sourceUser)
            ->create();

        $this->sourceUser
            ->deviceTransferService()
            ->create($this->targetUser, $device);
    }

    public function test_should_thrown_an_exception_when_the_device_status_is_analysis(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.validated'));

        $device = DeviceFactory::new()
            ->for($this->sourceUser)
            ->inAnalysis()
            ->create();

        $this->sourceUser
            ->deviceTransferService()
            ->create($this->targetUser, $device);
    }

    public function test_should_thrown_an_exception_when_the_device_status_is_rejected(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.validated'));

        $device = DeviceFactory::new()
            ->for($this->sourceUser)
            ->rejected()
            ->create();

        $this->sourceUser
            ->deviceTransferService()
            ->create($this->targetUser, $device);
    }

    public function test_should_thrown_an_exception_when_the_device_has_a_pending_transfer(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.transfer.pending'));

        $this->sourceUser
            ->deviceTransferService()
            ->create($this->targetUser, $this->device);

        $this->sourceUser
            ->deviceTransferService()
            ->create($this->targetUser, $this->device);
    }

    public function test_should_thrown_an_exception_when_occurs_an_internal_server_error(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.device_transfer.errors.create'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->sourceUser
            ->deviceTransferService()
            ->create($this->targetUser, $this->device);

    }
}
