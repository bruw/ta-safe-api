<?php

namespace Tests\Feature\Controllers\DeviceController\Register;

class RegisterDeviceAccessTest extends RegisterDeviceTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_authorized_to_register_a_device(): void
    {
        $this->assertAccessUnauthorizedTo(
            route: $this->route(),
            httpVerb: 'post',
            params: $this->data()
        );
    }

    public function test_an_authenticated_user_should_be_authorized_to_register_a_device(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertCreated',
            params: $this->data(),
            users: [$this->user]
        );
    }
}
