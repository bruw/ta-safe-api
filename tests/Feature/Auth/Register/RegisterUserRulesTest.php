<?php

namespace Tests\Feature\Auth\Register;

use App\Http\Messages\FlashMessage;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

class RegisterUserRulesTest extends RegisterUserTestSetUp
{
    public function test_should_return_all_errors_when_the_required_fields_are_null_values(): void
    {
        $this->postJson($this->route())
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.name.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.name'),
                    ]))
                    ->where('errors.email.0', trans('validation.required', [
                        'attribute' => 'email',
                    ]))
                    ->where('errors.cpf.0', trans('validation.required', [
                        'attribute' => 'cpf',
                    ]))
                    ->where('errors.phone.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.phone'),
                    ]))
                    ->where('errors.password.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.password'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_name_field_value_is_longer_than_255_characters(): void
    {
        $this->postJson($this->route(), $this->validUserData(['name' => Str::random(256)]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.name.0', trans('validation.max.string', [
                        'max' => 255,
                        'attribute' => trans('validation.attributes.name'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_email_field_value_is_not_a_valid_email(): void
    {
        $this->postJson($this->route(), $this->validUserData(['email' => Str::random(10)]))
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
        $this->postJson($this->route(), $this->validUserData(['email' => $this->user->email]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('validation.unique', [
                        'attribute' => 'email',
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_cpf_field_value_has_an_invalid_format(): void
    {
        $this->postJson($this->route(), $this->validUserData(['cpf' => Str::random(14)]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.cpf.0', trans('validation.regex', [
                        'attribute' => 'cpf',
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_cpf_field_values_exists_in_the_database(): void
    {
        $this->postJson($this->route(), $this->validUserData(['cpf' => $this->user->cpf]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.cpf.0', trans('validation.unique', [
                        'attribute' => 'cpf',
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_phone_param_is_invalid(): void
    {
        $this->postJson($this->route(), $this->validUserData(['phone' => Str::random(15)]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.phone.0', trans('validation.regex', [
                        'attribute' => trans('validation.attributes.phone'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_phone_field_value_exists_in_the_database(): void
    {
        $this->postJson($this->route(), $this->validUserData(['phone' => $this->user->phone]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.phone.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.phone'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_password_field_value_is_less_than_8_characters_longs(): void
    {
        $password = Str::random(7);

        $this->postJson($this->route(), $this->validUserData([
            'password' => $password,
            'password_confirmation' => $password,
        ]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.password.0', trans('validation.min.string', [
                        'attribute' => trans('validation.attributes.password'),
                        'min' => 8,
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_password_field_value_is_longer_than_255_characters(): void
    {
        $password = Str::random(256);

        $this->postJson($this->route(), $this->validUserData([
            'password' => $password,
            'password_confirmation' => $password,
        ]))
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

    public function test_should_return_an_error_when_the_password_is_different_from_the_password_confirmation(): void
    {
        $this->postJson($this->route(), $this->validUserData(['password_confirmation' => Str::random(8)]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.password.0', trans('validation.confirmed', [
                        'attribute' => trans('validation.attributes.password'),
                    ]))
            );
    }
}
