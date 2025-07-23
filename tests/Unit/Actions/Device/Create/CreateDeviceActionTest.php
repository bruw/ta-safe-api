<?php

namespace Tests\Unit\Actions\Device\Create;

use App\Enums\Device\DeviceValidationStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Device;
use Symfony\Component\HttpFoundation\Response;

class CreateDeviceActionTest extends CreateDeviceActionSetUpTest
{
    public function test_should_return_a_device_instance_when_the_action_is_successful(): void
    {
        $this->assertInstanceOf(Device::class, $this->user->createDevice($this->data));
    }

    public function test_should_persist_the_device_in_the_database(): void
    {
        $device = $this->user->createDevice($this->data);

        $this->assertDatabaseHas('devices', [
            'id' => $device->id,
            'user_id' => $this->user->id,
            'device_model_id' => $this->data->deviceModelId,
            'validation_status' => DeviceValidationStatus::PENDING,
            'color' => $this->data->color,
            'imei_1' => $this->data->imei1,
            'imei_2' => $this->data->imei2,
        ]);
    }

    public function test_should_persist_a_invoice_in_the_database(): void
    {
        $device = $this->user->createDevice($this->data);

        $this->assertDatabaseHas('invoices', [
            'device_id' => $device->id,
            'access_key' => $this->data->accessKey,
            'consumer_name' => null,
            'consumer_cpf' => null,
            'product_description' => null,
        ]);
    }

    public function test_should_increase_the_number_of_devices_linked_to_the_user(): void
    {
        $this->assertCount(1, $this->user->devices);

        $this->user->createDevice($this->data);
        $this->user->refresh();

        $this->assertCount(2, $this->user->devices);
    }

    public function test_should_thrown_an_exception_when_an_internal_error_occurs(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('device.errors.create'));

        $this->user->createDevice($this->invalidData);
        $this->user->refresh();
        $this->assertCount(0, $this->user->devices);
    }
}
