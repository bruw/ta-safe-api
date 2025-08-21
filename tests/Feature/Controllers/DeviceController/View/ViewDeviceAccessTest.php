<?php

namespace Tests\Feature\Controllers\DeviceController\View;

use Database\Factories\UserFactory;

class ViewDeviceAccessTest extends ViewDeviceTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_view_a_device(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'get');
    }

    public function test_the_owner_user_must_be_authorized_to_view_their_device(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'get',
            assertHttpResponse: 'assertOk',
            users: [$this->user]
        );
    }

    public function test_the_user_who_is_not_the_owner_of_the_device_should_not_be_allowed_to_view_it(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'get',
            users: [UserFactory::new()->create()]
        );
    }
}
