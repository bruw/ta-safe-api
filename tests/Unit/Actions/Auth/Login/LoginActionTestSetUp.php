<?php

namespace Tests\Unit\Actions\Auth\Login;

use App\Models\User;
use App\Services\Auth\AuthService;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginActionTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected AuthService $auth;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authSetUp();
        $this->userSetUp();
    }

    private function authSetUp(): void
    {
        $this->auth = new AuthService;
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }
}
