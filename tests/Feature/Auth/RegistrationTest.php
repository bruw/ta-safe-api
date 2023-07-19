<?php

namespace Tests\Feature\Auth;

use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::SetUp();
        $this->user = User::factory()->make();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertOk();

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->where('user.name', 'Test User')
                ->where('user.email',  fn (string $email) => str($email)->is('test@example.com'))
                ->has('user.id')
                ->has('user.createdAt')
                ->has('user.updatedAt')
                ->has('token')
                ->missing('user.password')
                ->etc()
        );
    }

    public function test_new_users_cannot_register_without_params()
    {
        $response = $this->postJson('/api/register', []);
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'name', 'email', 'password'
        ]);
    }

    public function test_new_users_cannot_register_invalid_names()
    {
        $invalidNames =  [
            null, "", Str::random(256)
        ];

        foreach ($invalidNames as $invalidName) {
            $response = $this->postJson('/api/register', [
                'name' => $invalidName,
                'email' => $this->user->name,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors('name');
        }
    }

    public function test_new_users_cannot_register_invalid_emails()
    {
        $invalidEmails =  [
            null, "", Str::random(256), 'example.email.com', 'example@.com'
        ];

        foreach ($invalidEmails  as $invalidEmail) {
            $response = $this->postJson('/api/register', [
                'name' => $this->user->name,
                'email' => $invalidEmail,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors('email');
        }
    }

    public function test_new_users_cannot_register_invalid_passwords()
    {
        $invalidPasswords =  [
            null, "", Str::random(7)
        ];

        foreach ($invalidPasswords  as $invalidPassword) {
            $response = $this->postJson('/api/register', [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $invalidPassword,
                'password_confirmation' => $invalidPassword,
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors('password');
        }
    }
}
