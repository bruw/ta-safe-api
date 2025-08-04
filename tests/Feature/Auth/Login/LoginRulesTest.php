<?php

namespace Tests\Feature\Auth\Login;

use App\Http\Messages\FlashMessage;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

class LoginRulesTest extends LoginTestSetUp
{
    public function test_should_return_all_errors_when_the_required_fields_are_null_values(): void
    {
        $this->postJson($this->route())
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('validation.required', [
                        'attribute' => 'email',
                    ]))
                    ->where('errors.password.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.password'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_email_field_value_is_not_a_valid_email(): void
    {
        $this->postJson($this->route(), [
            'email' => Str::random(10),
            'password' => '12345678',
        ])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('validation.email', [
                        'attribute' => 'email',
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_email_field_value_exists_in_the_database(): void
    {
        $this->postJson($this->route(), [
            'email' => fake()->email(),
            'password' => '12345678',
        ])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('auth.failed'))
            );
    }

    public function test_should_return_an_error_when_the_password_field_value_is_longer_than_255_characters(): void
    {
        $this->postJson($this->route(), [
            'email' => fake()->email(),
            'password' => Str::random(256),
        ])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.password.0', trans('validation.max.string', [
                        'attribute' => trans('validation.attributes.password'),
                        'max' => 255,
                    ]))
            );
    }
}
