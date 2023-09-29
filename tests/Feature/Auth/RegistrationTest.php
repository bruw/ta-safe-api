<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::SetUp();
        $this->seed(UserSeeder::class);

        $this->user = User::factory()->make();
    }

    public function test_new_users_must_be_authorized_to_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
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

    public function test_should_return_an_error_when_the_name_param_is_null(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => null,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.name', 1)
                    ->where('errors.name.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.name')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_name_param_is_longer_than_255_characters(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => Str::random(256),
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.name', 1)
                    ->where('errors.name.0', trans('validation.max.string', [
                        'max' => 255,
                        'attribute' => trans('validation.attributes.name')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_email_param_is_null(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => null,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.email.0', trans('validation.required', [
                        'attribute' => 'email'
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_email_param_already_exists_in_the_database(): void
    {
        $firstUser = User::firstOrFail();

        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $firstUser->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.email', 1)
                    ->where('errors.email.0', trans('validation.unique', [
                        'attribute' => 'email'
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_email_param_is_longer_than_255_characters(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => Str::random(256),
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.email', 2)
                    ->where('errors.email.0', trans('validation.email', [
                        'attribute' => 'email'
                    ]))
                    ->where('errors.email.1', trans('validation.max.string', [
                        'max' => 255,
                        'attribute' => 'email'
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_password_param_is_null(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => null,
            'password_confirmation' => null,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.password', 1)
                    ->where('errors.password.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.password')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_password_param_is_less_than_8_characters_longs(): void
    {
        $shortPassword = Str::random(7);

        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $shortPassword,
            'password_confirmation' => $shortPassword,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.password', 1)
                    ->where('errors.password.0', trans('validation.min.string', [
                        'min' => 8,
                        'attribute' => trans('validation.attributes.password')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_password_param_is_longer_than_255_characters(): void
    {
        $longPassword = Str::random(256);

        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $longPassword,
            'password_confirmation' => $longPassword,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.password', 1)
                    ->where('errors.password.0', trans('validation.max.string', [
                        'max' => 255,
                        'attribute' => trans('validation.attributes.password')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_password_is_different_from_the_password_confirmation(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => Str::random(10),
            'password_confirmation' => Str::random(10),
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.password', 1)
                    ->where('errors.password.0', trans('validation.confirmed', [
                        'min' => 8,
                        'attribute' => trans('validation.attributes.password')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_cpf_param_is_null(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => null,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.cpf', 1)
                    ->where('errors.cpf.0', trans('validation.required', [
                        'attribute' => 'cpf'
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_cpf_param_exists_in_the_database(): void
    {
        $firstUser = User::firstOrFail();

        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $firstUser->cpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.cpf', 1)
                    ->where('errors.cpf.0', trans('validation.unique', [
                        'attribute' => 'cpf'
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_cpf_param_has_an_invalid_format(): void
    {
        $invalidCpf = '00000000001';

        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $invalidCpf,
            'phone' => $this->user->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.cpf', 2)
                    ->where('errors.cpf.0', trans('validation.regex', [
                        'attribute' => 'cpf'
                    ]))
                    ->where('errors.cpf.1', trans('validation.custom.cpf.invalid_check_digits', [
                        'attribute' => 'cpf'
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_phone_param_is_null(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $this->user->cpf,
            'phone' => null
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.phone', 1)
                    ->where('errors.phone.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.phone')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_phone_param_already_exists_in_the_database(): void
    {
        $firstUser = User::firstOrFail();

        $response = $this->postJson('/api/register', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
            'cpf' => $this->user->cpf,
            'phone' => $firstUser->phone
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors.phone', 1)
                    ->where('errors.phone.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.phone')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_phone_param_is_invalid(): void
    {
        $invalidPhones =  [
            '00) 90000-0001', '(00 90000-0001',
            '(00)90000-0001', '(00) 90000000', '(00) 900000001', '(00) 90000-000',
            '(00) 90000-000a', '(x0) 90000-0001', '(00) 80000-0001'
        ];

        foreach ($invalidPhones as $phone) {
            $response = $this->postJson('/api/register', [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
                'cpf' => $this->user->cpf,
                'phone' => $phone
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJson(
                    fn (AssertableJson $json) =>
                    $json->has('errors.phone', 1)
                        ->where('errors.phone.0', trans('validation.regex', [
                            'attribute' => trans('validation.attributes.phone')
                        ]))
                        ->etc()
                );
        }
    }
}
