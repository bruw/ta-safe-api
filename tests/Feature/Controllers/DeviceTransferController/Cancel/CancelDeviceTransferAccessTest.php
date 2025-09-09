<?php

namespace Tests\Feature\Controllers\DeviceTransferController\Cancel;

use Database\Factories\UserFactory;

class CancelDeviceTransferAccessTest extends CancelDeviceTransferTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_cancel_a_device_transfer(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'post');
    }

    public function test_the_source_user_should_be_allowed_to_cancel_device_transfers_created_by_them(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertOk',
            users: [$this->sourceUser]
        );
    }

    public function test_users_should_not_be_allowed_to_cancel_device_transfers_created_by_another_user(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            users: [
                $this->targetUser,
                UserFactory::new()->create(),
            ]
        );
    }
}
