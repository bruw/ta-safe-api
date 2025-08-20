<?php

namespace Tests\Feature\Controllers\DeviceSharingController\View;

use App\Traits\StringMasks;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class ViewDeviceByTokenResponseTest extends ViewDeviceByTokenTestSetUp
{
    use StringMasks;

    protected function setUp(): void
    {
        parent::SetUp();
        Sanctum::actingAs($this->user);
    }

    public function test_should_return_the_expected_response_data_for_the_device(): void
    {
        $this->getJson($this->route($this->deviceSharingToken->token))
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('id', $this->device->id)
                    ->where('color', $this->device->color)
                    ->where('imei_1', $this->addAsteriskMaskForImei($this->device->imei_1))
                    ->where('imei_2', $this->addAsteriskMaskForImei($this->device->imei_2))
                    ->where('validation_status', $this->device->validation_status->value)
                    ->has('created_at')
                    ->has('updated_at')
                    ->has('validated_attributes')
                    ->has('transfers')
                    ->etc()
            );
    }

    public function test_should_return_the_expected_response_data_for_the_device_owner(): void
    {
        $this->getJson($this->route($this->deviceSharingToken->token))
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('user.id', $this->user->id)
                    ->where('user.name', $this->user->name)
                    ->where('user.cpf', $this->addAsteriskMaskForCpf($this->user->cpf))
                    ->where('user.phone', $this->addAsteriskMaskForPhone($this->user->phone))
                    ->has('user.created_at')
                    ->etc()
            );
    }

    public function test_should_return_the_expected_response_data_for_the_device_model(): void
    {
        $this->getJson($this->route($this->deviceSharingToken->token))
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('device_model.id', $this->device->deviceModel->id)
                    ->where('device_model.name', $this->device->deviceModel->name)
                    ->where('device_model.ram', $this->device->deviceModel->ram)
                    ->where('device_model.storage', $this->device->deviceModel->storage)
                    ->where('device_model.brand.id', $this->device->deviceModel->brand->id)
                    ->where('device_model.brand.name', $this->device->deviceModel->brand->name)
                    ->etc()
            );
    }
}
