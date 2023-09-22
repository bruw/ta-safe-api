<?php

namespace Tests\Feature\Factory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_must_correctly_instantiate_a_user_without_persisting_in_the_database(): void
    {
        $user = User::factory()->make();

        $this->assertInstanceOf(User::class, $user);
        $this->assertModelMissing($user);

        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->cpf);
        $this->assertNotNull($user->password);
    }

    public function test_must_correctly_instantiate_a_user_and_persist_in_the_database(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertModelExists($user);

        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->cpf);
        $this->assertNotNull($user->password);
    }
}
