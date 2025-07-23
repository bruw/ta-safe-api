<?php

namespace Tests\Feature\Controllers\DeviceController\Create;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;

class CreateDeviceRulesTest extends CreateDeviceSetUpTest
{
    public function test_should_return_errors_when_the_required_fields_are_null_values(): void
    {
        $this->actingAs($this->user)
            ->postJson($this->route())
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

    public function test_should_return_an_error_when_the_device_model_id_value_is_boolean(): void
    {
        $invalidIds = [true, false];

        foreach ($invalidIds as $id) {
            $this->actingAs($this->user)
                ->postJson($this->route(), $this->validDeviceData(['device_model_id' => $id]))
                ->assertUnprocessable()
                ->assertJson(
                    fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                        ->where('message.text', trans('flash_messages.errors'))
                        ->where('errors.device_model_id.0', trans('validation.not_boolean', [
                            'attribute' => trans('validation.attributes.device_model_id'),
                        ]))
                );
        }
    }

    public function test_should_return_an_error_when_the_device_model_id_value_does_not_exist_in_the_database(): void
    {
        $this->actingAs($this->user)
            ->postJson($this->route(), $this->validDeviceData(['device_model_id' => 0]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.device_model_id.0', trans('validation.exists', [
                        'attribute' => trans('validation.attributes.device_model_id'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_access_key_field_does_not_have_44_digits(): void
    {
        $this->actingAs($this->user)
            ->postJson($this->route(), $this->validDeviceData(['access_key' => $this->generateRandomNumber(43)]))
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

    public function test_should_return_an_error_when_the_access_key_field_does_not_unique_in_the_database(): void
    {
        $this->actingAs($this->user)
            ->postJson($this->route(), $this->validDeviceData(['access_key' => $this->device->invoice->access_key]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.access_key.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.access_key'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_color_field_is_longer_to_255_characters(): void
    {
        $this->actingAs($this->user)
            ->postJson($this->route(), $this->validDeviceData(['color' => $this->generateRandomNumber(256)]))
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

    public function test_should_return_an_error_when_the_imei_1_or_imei_2_field_does_not_have_15_digits(): void
    {
        $this->actingAs($this->user)
            ->postJson($this->route(), $this->validDeviceData([
                'imei_1' => $this->generateRandomNumber(14),
                'imei_2' => $this->generateRandomNumber(14),
            ]))
            ->assertUnprocessable()
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

    public function test_should_return_an_error_when_the_imei_1_and_imei_2_fields_are_the_same_value(): void
    {
        $imei = $this->generateRandomNumber(15);

        $this->actingAs($this->user)
            ->postJson($this->route(), $this->validDeviceData([
                'imei_1' => $imei,
                'imei_2' => $imei,
            ]))
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

    public function test_should_return_an_error_when_the_imei_1_or_imei_2_field_is_not_unique_in_the_database(): void
    {
        $this->actingAs($this->user)
            ->postJson($this->route(), $this->validDeviceData([
                'imei_1' => $this->device->imei_1,
                'imei_2' => $this->device->imei_2,
            ]))
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.imei_1.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.imei_1'),
                    ]))
                    ->where('errors.imei_2.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.imei_2'),
                    ]))
            );
    }
}
