<?php

namespace Tests\Unit\Actions\User\Update;

use App\Dto\User\UpdateUserDto;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateUserActionTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected UpdateUserDto $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userSetUp();
    }

    private function userSetUp(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        $this->user = UserFactory::new()->create();

        $this->data = new UpdateUserDto(
            name: $faker->name(),
            email: $faker->unique()->safeEmail(),
            phone: $faker->unique()->cellPhoneNumber(),
        );
    }
}
