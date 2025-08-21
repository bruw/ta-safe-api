<?php

namespace Tests\Feature\Controllers\UserController\Update;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class UpdateUserTestSetUp extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;

    protected User $user;
    protected User $anotherUser;

    protected function setUp(): void
    {
        parent::SetUp();
        $this->userSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
        $this->anotherUser = UserFactory::new()->create();
    }

    protected function route(): string
    {
        return route('api.user.update', $this->user);
    }

    protected function data(array $overrides = []): array
    {
        $faker = \Faker\Factory::create('pt_BR');

        return array_merge([
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'phone' => $faker->unique()->cellPhoneNumber(),
        ], $overrides);
    }
}
