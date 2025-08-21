<?php

namespace Tests\Feature\Controllers\UserController\Devices;

use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class UserDevicesResponseTest extends UserDevicesTestSetUp
{
    public function test_an_authenticated_user_should_receive_a_collection_of_devices(): void
    {
        Sanctum::actingAs($this->user);

        $this->getJson($this->route())
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has(1)
                    ->first(fn (AssertableJson $json) => $json->where('id', $this->device->id)
                        ->where('color', $this->device->color)
                        ->where('imei_1', $this->device->imei_1)
                        ->where('imei_2', $this->device->imei_2)
                        ->where('access_key', $this->device->invoice->access_key)
                        ->where('validation_status', $this->device->validation_status->value)
                        ->where('sharing_token', null)
                        ->has('created_at')
                        ->has('updated_at')
                        ->where('user.id', $this->user->id)
                        ->where('user.name', $this->user->name)
                        ->where('user.cpf', $this->user->cpf)
                        ->where('user.phone', $this->user->phone)
                        ->has('user.created_at')
                        ->has('user.updated_at')
                        ->missing('user.password')
                        ->where('device_model.name', $this->device->deviceModel->name)
                        ->where('device_model.ram', $this->device->deviceModel->ram)
                        ->where('device_model.storage', $this->device->deviceModel->storage)
                        ->where('device_model.brand.name', $this->device->deviceModel->brand->name)
                        ->has('validated_attributes')
                        ->has('transfers')
                    ));
    }
}
