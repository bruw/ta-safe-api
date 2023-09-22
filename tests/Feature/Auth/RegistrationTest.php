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
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $this->assertAuthenticated();
        $response->assertOk();

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('user.id')
                ->has('user.created_at')
                ->has('user.updated_at')
                ->has('token')
                ->where('user.name', $this->user->name)
                ->where('user.email',  fn (string $email) => str($email)->is($this->user->email))
                ->where('user.cpf', fn (string $cpf) => str($cpf)->is($this->user->cpf))
                ->where('user.phone', fn (string $phone) => str($phone)->is($this->user->phone))
                ->missing('user.password')
                ->etc()
        );
    }

    public function test_new_users_cannot_register_without_params()
    {
        $response = $this->postJson('/api/register', []);
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'name', 'email', 'password', 'cpf', 'phone'
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
                'cpf' => $this->user->cpf,
                'phone' => $this->user->phone
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
                'cpf' => $this->user->cpf,
                'phone' => $this->user->phone
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
                'cpf' => $this->user->cpf,
                'phone' => $this->user->phone
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors('password');
        }
    }

    public function test_password_confirmation_field_validation_is_working()
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => fake()->password(10, 16),
            'password_confirmation' => fake()->password(10, 16) . 0,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('password');
    }

    public function test_new_users_cannot_register_invalid_cpf()
    {
        $invalidCpfs =  [
            null, "", true, false, Str::random(14), '00000000000', '000.000.000.00',
            '000-000-000-00', 'a00.000.000-01', '000.000.000-a1', '23251376276',
            '062.387.624.89', '118-721-712-30'
        ];

        foreach ($invalidCpfs as $invalidCpf) {
            $response = $this->postJson('/api/register', [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
                'cpf' => $invalidCpf,
                'phone' => $this->user->phone
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors('cpf');
        }
    }

    public function test_new_users_cannot_register_invalid_phone()
    {
        $invalidPhones =  [
            null, "", true, false, Str::random(15), '00) 90000-0001', '(00 90000-0001',
            '(00)90000-0001', '(00) 90000000', '(00) 900000001', '(00) 90000-000',
            '(00) 90000-000a', '(x0) 90000-0001', '(00) 80000-0001'
        ];

        foreach ($invalidPhones as $invalidPhone) {
            $response = $this->postJson('/api/register', [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
                'cpf' => $this->user->cpf,
                'phone' => $invalidPhone
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors('phone');
        }
    }
}
