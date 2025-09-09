<?php

namespace Tests\Feature\Controllers\DeviceTransferController\Create;

class CreateDeviceTransferAccessTest extends CreateDeviceTransferTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_create_device_transfers(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'post');
    }

    public function test_the_owner_of_the_device_must_be_authorized_to_create_transfers(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertCreated',
            params: ['target_user_id' => $this->targetUser->id],
            flashMessage: $this->flashMessage(),
            users: [$this->sourceUser]
        );
    }

    public function test_a_user_should_not_be_allowed_to_create_a_device_transfer_for_a_device_that_does_not_belong_to_them(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            params: ['target_user_id' => $this->sourceUser->id],
            users: [$this->targetUser]
        );
    }
}
