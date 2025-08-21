<?php

namespace Tests\Feature\Controllers\UserController\Update;

use App\Http\Messages\FlashMessage;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class UpdateUserRuleTest extends UpdateUserTestSetUp
{
    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs($this->user);
    }

    public function test_should_return_errors_when_the_required_fields_are_null(): void
    {
        $this->patchJson($this->route())
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
                    ->where('errors.phone.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.phone'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_name_field_is_greater_than_255_characters(): void
    {
        $this->patchJson($this->route(), $this->data(['name' => Str::random(256)]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.name.0', trans('validation.max.string', [
                        'attribute' => trans('validation.attributes.name'),
                        'max' => 255,
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_email_field_is_invalid(): void
    {
        $this->patchJson($this->route(), $this->data(['email' => 'abc']))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('validation.email', [
                        'attribute' => 'email',
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_email_is_not_unique(): void
    {
        $this->patchJson($this->route(), $this->data(['email' => $this->anotherUser->email]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('validation.unique', [
                        'attribute' => 'email',
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_phone_field_is_not_unique(): void
    {
        $this->patchJson($this->route(), $this->data(['phone' => $this->anotherUser->phone]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.phone.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.phone'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_phone_field_regex_is_not_valid(): void
    {
        $this->patchJson($this->route(), $this->data(['phone' => '42999999999']))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.phone.0', trans('validation.regex', [
                        'attribute' => trans('validation.attributes.phone'),
                    ]))
            );
    }
}
