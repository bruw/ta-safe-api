<?php

namespace Tests\Unit\Actions\Device\Invalidate;

use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class InvalidateDeviceActionTest extends InvalidateDeviceActionTestSetUp
{
    public function test_should_return_a_device_instance_when_the_action_is_executed_successfully(): void
    {
        $result = $this->user->deviceService()->invalidate($this->device);
        $this->assertInstanceOf(Device::class, $result);
    }

    public function test_should_change_the_device_status_to_rejected(): void
    {
        $this->user->deviceService()->invalidate($this->device);

        $this->assertDatabaseHas('devices', [
            'id' => $this->device->id,
            'user_id' => $this->user->id,
            'validation_status' => DeviceValidationStatus::REJECTED,
        ]);
    }

    public function test_should_thrown_an_exception_when_the_user_is_not_the_device_owner(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);
        $this->expectExceptionMessage(trans('validators.device.user.owner'));

        $user = UserFactory::new()->create();
        $user->deviceService()->invalidate($this->device);
    }

    public function test_should_thrown_an_exception_when_the_device_status_is_in_analysis(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.pending'));

        $device = DeviceFactory::new()
            ->for($this->user)
            ->inAnalysis()
            ->create();

        $this->user->deviceService()->invalidate($device);
    }

    public function test_should_thrown_an_exception_when_the_device_status_is_validated(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.pending'));

        $device = DeviceFactory::new()
            ->for($this->user)
            ->validated()
            ->create();

        $this->user->deviceService()->invalidate($device);
    }

    public function test_should_thrown_an_exception_when_the_device_status_is_rejected(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.pending'));

        $device = DeviceFactory::new()
            ->for($this->user)
            ->rejected()
            ->create();

        $this->user->deviceService()->invalidate($device);
    }

    public function test_should_thrown_an_exception_when_occurs_an_internal_error_server(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.device.errors.invalidate'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->user->deviceService()->invalidate($this->device);
    }
}
