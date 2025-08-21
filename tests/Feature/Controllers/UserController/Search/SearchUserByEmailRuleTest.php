<?php

namespace Tests\Feature\Controllers\UserController\Search;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class SearchUserByEmailRuleTest extends SearchUserByEmailTestSetUp
{
    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs($this->user);
    }

    public function test_should_return_an_error_when_the_email_field_is_null(): void
    {
        $this->getJson($this->route())
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('validation.required', [
                        'attribute' => 'email',
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_email_field_is_not_valid(): void
    {
        $this->getJson($this->route(email: 'abc'))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('validation.email', [
                        'attribute' => 'email',
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_email_field_is_not_exists(): void
    {
        $this->getJson($this->route(email: 'abc@abc.com'))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('validation.custom.email.exists', [
                        'attribute' => 'email',
                    ]))
            );
    }
}
