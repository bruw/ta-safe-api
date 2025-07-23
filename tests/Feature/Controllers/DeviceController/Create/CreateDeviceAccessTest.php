<?php

namespace Tests\Feature\Controllers\DeviceController\Create;

class CreateDeviceAccessTest extends CreateDeviceSetUpTest
{
    public function test_an_unauthenticated_user_must_not_be_authorized_to_register_a_device(): void
    {
        $this->assertAccessUnauthorizedTo(route: $this->route(), httpVerb: 'post');
    }

    public function test_an_authenticated_user_must_be_authorized_to_register_a_device(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertCreated',
            flashMessage: $this->successCreatedDevice(),
            users: [$this->user],
            params: [
                'device_model_id' => $this->device->deviceModel->id,
                'access_key' => $this->generateRandomNumber(44),
                'color' => 'white',
                'imei_1' => $this->generateRandomNumber(15),
                'imei_2' => $this->generateRandomNumber(15),
            ],
        );
    }
}
