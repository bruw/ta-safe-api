<?php

namespace Tests\Feature\Controllers\UserController\View;

class ViewUserAccessTest extends ViewUserTestSetUp
{
    public function test_an_unauthenticated_user_should_not_allowed_to_view_their_profile(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'get');
    }

    public function test_an_authenticated_user_should_be_allowed_to_view_their_profile(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'get',
            assertHttpResponse: 'assertOk',
            users: [$this->user]
        );
    }
}
