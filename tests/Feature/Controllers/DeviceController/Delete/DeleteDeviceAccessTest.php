<?php

namespace Tests\Feature\Controllers\DeviceController\Delete;

use Database\Factories\UserFactory;

class DeleteDeviceAccessTest extends DeleteDeviceTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_authorized_to_delete_a_device(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'post');
    }

    public function test_a_user_should_be_authorized_to_delete_their_invalid_devices(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'delete',
            assertHttpResponse: 'assertOk',
            users: [$this->user],
        );
    }

    public function test_a_user_should_not_be_authorized_to_delete_another_user_devices(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'delete',
            users: [UserFactory::new()->create()],
        );
    }
}
