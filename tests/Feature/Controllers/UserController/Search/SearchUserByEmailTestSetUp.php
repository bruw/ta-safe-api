<?php

namespace Tests\Feature\Controllers\UserController\Search;

use App\Models\User;
use App\Traits\StringMasks;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class SearchUserByEmailTestSetUp extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;
    use StringMasks;

    protected User $user;
    protected User $targetUser;

    protected function setUp(): void
    {
        parent::SetUp();
        $this->userSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
        $this->targetUser = UserFactory::new()->create();
    }

    protected function route(?string $email = null): string
    {
        return route('api.user.search', ['email' => $email]);
    }
}
