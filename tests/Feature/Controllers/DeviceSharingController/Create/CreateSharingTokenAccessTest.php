<?php

namespace Tests\Feature\Controllers\DeviceSharingController\Create;

use Database\Factories\UserFactory;

class CreateSharingTokenAccessTest extends CreateSharingTokenTestSetUp
{
    public function test_an_unauthenticated_user_must_not_be_allowed_to_create_a_device_data_sharing_token(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'post');
    }

    public function test_the_owner_user_must_be_authorized_to_generate_a_device_data_sharing_token(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertCreated',
            users: [$this->user]
        );
    }

    public function test_a_user_who_is_not_the_owner_of_the_device_should_not_be_allowed_to_create_a_device_data_sharing_token(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            users: [UserFactory::new()->create()]
        );
    }
}
