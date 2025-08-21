<?php

namespace Tests\Feature\Controllers\UserController\Update;

class UpdateUserAccessTest extends UpdateUserTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_update_profile(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'patch');
    }

    public function test_an_authenticated_user_should_be_allowed_to_update_your_profile(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'patch',
            assertHttpResponse: 'assertOk',
            params: $this->data(),
            users: [$this->user]
        );
    }

}
