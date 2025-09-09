<?php

namespace Tests\Feature\Controllers\DeviceController\Invalidation;

use Database\Factories\UserFactory;

class InvalidateDeviceAccessTest extends InvalidateDeviceTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_invalidate_a_device_registration(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'post');
    }

    public function test_user_who_owns_the_device_must_be_allowed_to_invalidate_the_registration(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertOk',
            users: [$this->user]
        );
    }

    public function test_the_user_who_does_not_own_the_device_must_not_be_allowed_to_invalidate_the_registration(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            users: [UserFactory::new()->create()]
        );
    }
}
