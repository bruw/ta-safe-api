<?php

namespace Tests\Feature\Controllers\UserController\Search;

class SearchUserByEmailAccessTest extends SearchUserByEmailTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_allowed_to_search_users_by_email(): void
    {
        $this->assertAccessUnauthorizedTo($this->route(), 'get');
    }

    public function test_an_authenticated_user_should_be_allowed_to_search_users_by_email(): void
    {
        $this->assertAccessTo(
            route: $this->route(email: $this->targetUser->email),
            httpVerb: 'get',
            assertHttpResponse: 'assertOk',
            users: [$this->user]
        );
    }
}
