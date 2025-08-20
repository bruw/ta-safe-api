<?php

namespace Tests\Feature\Controllers\DeviceSharingController\View;

use App\Http\Messages\FlashMessage;
use Database\Factories\DeviceSharingTokenFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class ViewDeviceByTokenRulesTest extends ViewDeviceByTokenTestSetUp
{
    protected function setUp(): void
    {
        parent::SetUp();
        Sanctum::actingAs($this->user);
    }

    public function test_should_return_an_error_when_the_token_param_is_null(): void
    {
        $this->getJson($this->route())
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.token.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.token'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_token_size_is_not_8(): void
    {
        $token = '1234567';

        $this->getJson($this->route($token))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.token.0', trans('validation.size.string', [
                        'attribute' => trans('validation.attributes.token'),
                        'size' => 8,
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_token_not_exists(): void
    {
        $token = '12345678';

        $this->getJson($this->route($token))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.token.0', trans('validation.custom.token.exists', [
                        'attribute' => trans('validation.attributes.token'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_token_is_expired(): void
    {
        $token = DeviceSharingTokenFactory::new()->expired()->create()->token;

        $this->getJson($this->route($token))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.token.0', trans('validation.custom.token.expired', [
                        'attribute' => trans('validation.attributes.token'),
                    ]))
            );
    }
}
