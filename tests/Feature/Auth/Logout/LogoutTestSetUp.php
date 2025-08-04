<?php

namespace Tests\Feature\Auth\Logout;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
        $this->token = $this->user->createToken('auth-token');
    }
}
