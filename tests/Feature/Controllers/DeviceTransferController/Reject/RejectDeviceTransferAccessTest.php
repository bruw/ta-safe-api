<?php

namespace Tests\Feature\Controllers\DeviceTransferController\Reject;

use Database\Factories\UserFactory;

class RejectDeviceTransferAccessTest extends RejectDeviceTransferTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_reject_a_device_transfer(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'post');
    }

    public function test_the_target_user_should_be_allowed_to_reject_a_device_transfer(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertOk',
            users: [$this->targetUser]
        );
    }

    public function test_a_user_who_is_not_the_recipient_of_the_device_transfer_should_not_be_allowed_to_reject_it(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            users: [
                $this->sourceUser,
                UserFactory::new()->create(),
            ]
        );
    }
}
