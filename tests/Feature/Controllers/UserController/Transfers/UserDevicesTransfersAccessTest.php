<?php

namespace Tests\Feature\Controllers\UserController\Transfers;

class UserDevicesTransfersAccessTest extends UserDevicesTransfersTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_view_devices_transfers(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'get');
    }

    public function test_an_authenticated_user_should_be_allowed_to_view_their_devices_transfers(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'get',
            assertHttpResponse: 'assertOk',
            users: [$this->user]
        );
    }
}
