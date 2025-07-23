<?php

namespace Tests\Feature\Controllers\DeviceController\Delete;

use Database\Factories\UserFactory;

class DeleteDeviceAccessTest extends DeleteDeviceSetUpTest
{
    public function test_an_unauthenticated_user_should_not_be_authorized_to_delete_a_device(): void
    {
        $this->assertAccessUnauthorizedTo(route: $this->route(), httpVerb: 'delete');
    }

    public function test_an_authenticated_user_should_be_allowed_to_delete_their_devices(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'delete',
            assertHttpResponse: 'assertOk',
            flashMessage: $this->successDeleteDevice(),
            users: [$this->user]
        );
    }

    public function test_a_user_should_not_be_allowed_to_delete_other_users_devices(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'delete',
            users: [UserFactory::new()->create()]
        );
    }
}
