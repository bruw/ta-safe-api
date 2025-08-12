<?php

namespace Tests\Unit\Actions\Device\Delete;

use App\Exceptions\HttpJsonResponseException;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Support\Facades\DB as FacadesDB;
use Symfony\Component\HttpFoundation\Response;

class DeleteDeviceActionTest extends DeleteDeviceActionTestSetUp
{
    public function test_should_return_true_when_the_device_is_deleted(): void
    {
        $this->assertTrue($this->user->deviceService()->delete($this->device));
    }

    public function test_should_decrement_the_total_devices_of_the_user(): void
    {
        $this->assertCount(1, $this->user->devices);

        $this->user->deviceService()->delete($this->device);
        $this->user->refresh();

        $this->assertCount(0, $this->user->devices);
    }

    public function test_should_not_allow_the_delete_of_a_device_that_does_not_belong_to_the_user(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);
        $this->expectExceptionMessage(trans('validators.device.user.owner'));

        $user = UserFactory::new()->create();
        $user->deviceService()->delete($this->device);
    }

    public function test_should_not_delete_a_device_when_the_status_is_validated(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.rejected'));

        $device = DeviceFactory::new()
            ->for($this->user)
            ->validated()
            ->create();

        $this->user->deviceService()->delete($device);
    }

    public function test_should_not_delete_a_device_when_the_status_is_pending(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.rejected'));

        $device = DeviceFactory::new()
            ->for($this->user)
            ->create();

        $this->user->deviceService()->delete($device);
    }

    public function test_should_not_delete_a_device_when_the_status_is_in_analysis(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.rejected'));

        $device = DeviceFactory::new()
            ->for($this->user)
            ->inAnalysis()
            ->create();

        $this->user->deviceService()->delete($device);
    }

    public function test_should_thrown_an_exception_when_occurs_an_internal_server_error(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.device.errors.delete'));

        FacadesDB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->user->deviceService()->delete($this->device);
    }
}
