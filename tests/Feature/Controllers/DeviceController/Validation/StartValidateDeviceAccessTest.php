<?php

namespace Tests\Feature\Controllers\DeviceController\Validation;

use Database\Factories\UserFactory;

class StartValidateDeviceAccessTest extends StartValidateDeviceTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_validate_a_device_record(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'post');
    }

    public function test_the_user_who_is_owns_the_device_should_be_allowed_to_validate_its_registration(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertOk',
            params: $this->data(),
            users: [$this->user]
        );
    }

    public function test_a_user_should_not_be_allowed_to_validate_a_device_that_does_not_belong_to_them(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            params: $this->data(),
            users: [UserFactory::new()->create()]
        );
    }
}
