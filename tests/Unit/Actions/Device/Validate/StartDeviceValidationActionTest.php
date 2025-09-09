<?php

namespace Tests\Unit\Actions\Device\Validate;

use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StartDeviceValidationActionTest extends StartDeviceValidationActionTestSetUp
{
    public function test_should_return_a_device_instance_when_the_action_is_executed_successfully(): void
    {
        $result = $this->user->deviceService()->validate($this->device, $this->data);
        $this->assertInstanceOf(Device::class, $result);
    }

    public function test_should_change_the_device_status_to_in_analysis(): void
    {
        $this->user->deviceService()->validate($this->device, $this->data);

        $this->assertDatabaseHas('devices', [
            'id' => $this->device->id,
            'user_id' => $this->user->id,
            'validation_status' => DeviceValidationStatus::IN_ANALYSIS,
        ]);
    }

    public function test_should_update_the_invoice_with_the_data(): void
    {
        $this->user->deviceService()->validate($this->device, $this->data);

        $this->assertDatabaseHas('invoices', [
            'id' => $this->device->invoice->id,
            'device_id' => $this->device->id,
            'consumer_cpf' => $this->data->cpf,
            'consumer_name' => $this->data->name,
            'product_description' => $this->data->products,
        ]);
    }

    public function test_should_thrown_an_exception_when_the_user_is_not_the_device_owner(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);
        $this->expectExceptionMessage(trans('validators.device.user.owner'));

        $user = UserFactory::new()->create();
        $user->deviceService()->validate($this->device, $this->data);
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

        $this->user->deviceService()->validate($device, $this->data);
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

        $this->user->deviceService()->validate($device, $this->data);
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

        $this->user->deviceService()->validate($device, $this->data);
    }

    public function test_should_thrown_an_exception_when_occurs_an_internal_error_server(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.device.errors.validate'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->user->deviceService()->validate($this->device, $this->data);
    }
}
