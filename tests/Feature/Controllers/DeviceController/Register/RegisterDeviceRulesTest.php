<?php

namespace Tests\Feature\Controllers\DeviceController\Register;

use App\Http\Messages\FlashMessage;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class RegisterDeviceRulesTest extends RegisterDeviceTestSetUp
{
    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs($this->user);
    }

    public function test_should_return_many_errors_when_the_required_fields_is_null_values(): void
    {
        $this->postJson($this->route())
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.device_model_id.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.device_model_id'),
                    ]))
                    ->where('errors.access_key.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.access_key'),
                    ]))
                    ->where('errors.color.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.color'),
                    ]))
                    ->where('errors.imei_1.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.imei_1'),
                    ]))
                    ->where('errors.imei_2.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.imei_2'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_device_model_id_field_is_not_integer(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson(
            $this->route(),
            $this->data(['device_model_id' => Str::random(4)])
        )
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.device_model_id.0', trans('validation.integer', [
                        'attribute' => trans('validation.attributes.device_model_id'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_device_model_id_field_does_not_exists(): void
    {
        $this->postJson(
            $this->route(),
            $this->data(['device_model_id' => 0])
        )
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.device_model_id.0', trans('validation.exists', [
                        'attribute' => trans('validation.attributes.device_model_id'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_color_field_is_longer_than_255_characters(): void
    {
        $this->postJson(
            $this->route(),
            $this->data(['color' => Str::random(256)])
        )
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.color.0', trans('validation.max.string', [
                        'attribute' => trans('validation.attributes.color'),
                        'max' => 255,
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_access_key_field_is_longer_than_44_characters(): void
    {
        $this->postJson(
            $this->route(),
            $this->data(['access_key' => $this->generateRandomNumber(45)])
        )
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.access_key.0', trans('validation.digits', [
                        'attribute' => trans('validation.attributes.access_key'),
                        'digits' => 44,
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_access_key_field_value_already_in_use(): void
    {
        $this->postJson(
            $this->route(),
            $this->data(['access_key' => $this->device->invoice->access_key])
        )
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.access_key.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.access_key'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_imei_fields_is_longer_than_15_characters(): void
    {
        $this->postJson(
            $this->route(),
            $this->data([
                'imei_1' => $this->generateRandomNumber(16),
                'imei_2' => $this->generateRandomNumber(16),
            ]))->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.imei_1.0', trans('validation.digits', [
                        'attribute' => trans('validation.attributes.imei_1'),
                        'digits' => 15,
                    ]))
                    ->where('errors.imei_2.0', trans('validation.digits', [
                        'attribute' => trans('validation.attributes.imei_2'),
                        'digits' => 15,
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_imei_fields_are_the_same_values(): void
    {
        Sanctum::actingAs($this->user);

        $imei = $this->generateRandomNumber(15);

        $this->postJson(
            $this->route(),
            $this->data(['imei_1' => $imei, 'imei_2' => $imei])
        )
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.imei_1.0', trans('validation.different', [
                        'attribute' => trans('validation.attributes.imei_1'),
                        'other' => trans('validation.attributes.imei_2'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_imei_1_field_value_already_in_use(): void
    {
        Sanctum::actingAs($this->user);

        $inUse = [$this->device->imei_1, $this->device->imei_2];

        foreach ($inUse as $imei) {
            $this->postJson(
                $this->route(),
                $this->data(['imei_1' => $imei])
            )
                ->assertUnprocessable()
                ->assertJson(
                    fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                        ->where('message.text', trans('flash_messages.errors'))
                        ->where('errors.imei_1.0', trans('validation.unique', [
                            'attribute' => trans('validation.attributes.imei_1'),
                        ]))
                );
        }
    }

    public function test_should_return_an_error_when_the_imei_2_field_value_already_in_use(): void
    {
        Sanctum::actingAs($this->user);

        $inUse = [$this->device->imei_1, $this->device->imei_2];

        foreach ($inUse as $imei) {
            $this->postJson(
                $this->route(),
                $this->data(['imei_2' => $imei])
            )
                ->assertUnprocessable()
                ->assertJson(
                    fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                        ->where('message.text', trans('flash_messages.errors'))
                        ->where('errors.imei_2.0', trans('validation.unique', [
                            'attribute' => trans('validation.attributes.imei_2'),
                        ]))
                );
        }
    }

}
