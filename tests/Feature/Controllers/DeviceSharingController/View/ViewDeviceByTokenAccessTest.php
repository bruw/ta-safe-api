<?php

namespace Tests\Feature\Controllers\DeviceSharingController\View;

class ViewDeviceByTokenAccessTest extends ViewDeviceByTokenTestSetUp
{
    public function test_an_unauthenticated_user_must_not_be_authorized_to_view_a_device(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'get');
    }

    public function test_an_authenticated_user_with_a_valid_token_must_be_authorized_to_view_a_device(): void
    {
        $this->assertAccessTo(
            route: $this->route($this->deviceSharingToken->token),
            httpVerb: 'get',
            assertHttpResponse: 'assertOk',
            users: [$this->user]
        );
    }
}
