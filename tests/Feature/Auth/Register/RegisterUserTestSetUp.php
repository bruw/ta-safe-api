<?php

namespace Tests\Feature\Auth\Register;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
    }

    protected function route(): string
    {
        return route('api.auth.register');
    }

    protected function validUserData(array $overrides = []): array
    {
        $faker = \Faker\Factory::create('pt_BR');

        return array_merge([
            'name' => $faker->name(),
            'email' => $faker->unique()->email(),
            'cpf' => $faker->unique()->cpf(),
            'phone' => $faker->unique()->cellPhoneNumber(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $overrides);
    }
}
