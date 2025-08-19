<?php

namespace Tests\Feature\Controllers\DeviceTransferController\Accept;

use Database\Factories\UserFactory;

class AcceptDeviceTransferAccessTest extends AcceptDeviceTransferTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_accept_a_device_transfer(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'post');
    }

    public function test_the_target_user_should_be_allowed_to_accept_a_device_transfer_sent_to_them(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertOk',
            users: [$this->targetUser]
        );
    }

    public function test_should_not_be_accept_device_transfer_from_another_user(): void
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
