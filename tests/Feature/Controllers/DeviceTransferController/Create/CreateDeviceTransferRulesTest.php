<?php

namespace Tests\Feature\Controllers\DeviceTransferController\Create;

use App\Http\Messages\FlashMessage;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class CreateDeviceTransferRulesTest extends CreateDeviceTransferTestSetUp
{
    protected function setUp(): void
    {
        parent::SetUp();
        Sanctum::actingAs($this->sourceUser);
    }

    public function test_should_return_an_error_when_the_target_user_id_param_is_null(): void
    {
        $this->postJson($this->route())
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.target_user_id.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.target_user_id'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_target_user_id_param_is_not_numeric(): void
    {
        $this->postJson($this->route(), ['target_user_id' => Str::random(4)])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.target_user_id.0', trans('validation.integer', [
                        'attribute' => trans('validation.attributes.target_user_id'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_target_user_id_param_does_not_exist_in_the_database(): void
    {
        $this->postJson($this->route(), ['target_user_id' => 0])
            ->assertUnprocessable()
            ->assertJson(fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.target_user_id.0', trans('validation.exists', [
                    'attribute' => trans('validation.attributes.target_user_id'),
                ]))
            );
    }
}
