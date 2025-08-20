<?php

namespace Tests\Feature\Controllers\DeviceController\Validation;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;

class StartValidateDeviceRulesTest extends StartValidateDeviceTestSetUp
{
    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs($this->user);
    }

    public function test_should_return_an_errors_when_the_required_fields_are_null(): void
    {
        $this->postJson($this->route())
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.cpf.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.cpf'),
                    ]))
                    ->where('errors.name.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.name'),
                    ]))
                    ->where('errors.products.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.products'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_cpf_field_is_longer_than_16_characters(): void
    {
        $this->postJson($this->route(), $this->data(['cpf' => $this->generateRandomNumber(17)]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.cpf.0', trans('validation.max.string', [
                        'attribute' => trans('validation.attributes.cpf'),
                        'max' => 16,
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_name_field_is_longer_than_255_characters(): void
    {
        $this->postJson($this->route(), $this->data(['name' => $this->generateRandomNumber(256)]))
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

    public function test_should_return_an_error_when_the_products_field_is_longer_than_16000_characters(): void
    {
        $this->postJson($this->route(), $this->data(['products' => Str::random(16001)]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.products.0', trans('validation.max.string', [
                        'attribute' => trans('validation.attributes.products'),
                        'max' => 16000,
                    ]))
            );
    }
}
