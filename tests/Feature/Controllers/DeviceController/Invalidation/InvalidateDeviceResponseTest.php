<?php

namespace Tests\Feature\Controllers\DeviceController\Invalidation;

use App\Enums\Device\DeviceValidationStatus;
use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class InvalidateDeviceResponseTest extends InvalidateDeviceTestSetUp
{
    public function test_should_return_the_expected_response_data_and_structure(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson($this->route())
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                    ->where('message.text', trans('actions.device.success.invalidate'))
                    ->where('device.id', $this->device->id)
                    ->where('device.color', $this->device->color)
                    ->where('device.imei_1', $this->device->imei_1)
                    ->where('device.imei_2', $this->device->imei_2)
                    ->where('device.access_key', $this->device->invoice->access_key)
                    ->where('device.validation_status', DeviceValidationStatus::REJECTED->value)
                    ->has('device.sharing_token')
                    ->has('device.created_at')
                    ->has('device.updated_at')
                    ->has('device.user')
                    ->has('device.device_model')
                    ->has('device.device_model.brand')
                    ->etc()
            );
    }
}
