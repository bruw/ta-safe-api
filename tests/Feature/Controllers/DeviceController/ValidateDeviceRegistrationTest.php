<?php

namespace Tests\Feature\Controllers\DeviceController;

use App\Enums\Device\DeviceValidationStatus;
use App\Http\Messages\FlashMessage;
use App\Models\Device;
use App\Models\User;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\InvoiceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ValidateDeviceRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $anotherUser;
    private Device $deviceNotValidated;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->deviceSetUp();
        $this->invoiceSetUp();
    }

    /*
    ================= **START OF SETUP** ==========================================================================
    */

    private function userSetUp(): void
    {
        $this->owner = UserFactory::new()->create();
        $this->anotherUser = UserFactory::new()->create();
    }

    private function deviceSetUp(): void
    {
        $deviceModel = DeviceModelFactory::new()
            ->for(BrandFactory::new()->create())
            ->create();

        $this->deviceNotValidated = DeviceFactory::new()
            ->for($this->owner)
            ->for($deviceModel)
            ->create();
    }

    private function invoiceSetUp(): void
    {
        InvoiceFactory::new()
            ->for($this->deviceNotValidated)
            ->create();
    }

    private function validateDeviceRoute($deviceId): string
    {
        return "api/devices/{$deviceId}/validate";
    }

    /*
    ================= **START OF TESTS** ==========================================================================
    */

    public function test_an_unauthenticated_user_should_not_be_allowed_to_validate_a_device_registration(): void
    {
        $route = $this->validateDeviceRoute($this->deviceNotValidated->id);

        $response = $this->postJson($route, [
            'cpf' => $this->owner->cpf,
            'name' => $this->owner->name,
            'products' => fake()->text(),
        ]);

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_user_who_owns_the_device_must_be_able_to_validate_the_registration(): void
    {
        Sanctum::actingAs($this->owner);

        $route = $this->validateDeviceRoute($this->deviceNotValidated->id);

        $response = $this->postJson($route, [
            'cpf' => $this->owner->cpf,
            'name' => $this->owner->name,
            'products' => fake()->text(),
        ]);

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans('actions.device_validation.start'))
                ->where('device.id', $this->deviceNotValidated->id)
                ->where('device.color', $this->deviceNotValidated->color)
                ->where('device.imei_1', $this->deviceNotValidated->imei_1)
                ->where('device.imei_2', $this->deviceNotValidated->imei_2)
                ->where('device.access_key', $this->deviceNotValidated->invoice->access_key)
                ->where('device.validation_status', DeviceValidationStatus::IN_ANALYSIS->value)
                ->has('device.sharing_token')
                ->has('device.created_at')
                ->has('device.updated_at')
                ->has('device.user')
                ->has('device.device_model')
                ->has('device.device_model.brand')
                ->etc()
        );
    }

    public function test_the_user_who_does_not_own_the_device_must_not_be_able_to_validate_the_registration(): void
    {
        Sanctum::actingAs($this->anotherUser);

        $route = $this->validateDeviceRoute($this->deviceNotValidated->id);

        $response = $this->postJson($route, [
            'cpf' => $this->owner->cpf,
            'name' => $this->owner->name,
            'products' => fake()->text(),
        ]);

        $response->assertForbidden()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthorized'))
        );
    }

    public function test_should_return_an_error_when_the_params_is_null_value(): void
    {
        Sanctum::actingAs($this->owner);

        $route = $this->validateDeviceRoute($this->deviceNotValidated->id);

        $response = $this->postJson($route, [
            'cpf' => null,
            'name' => null,
            'products' => null,
        ]);

        $response->assertUnprocessable()->assertJson(
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

    public function test_should_return_an_error_when_the_params_values_is_not_string(): void
    {
        Sanctum::actingAs($this->owner);

        $route = $this->validateDeviceRoute($this->deviceNotValidated->id);

        $response = $this->postJson($route, [
            'cpf' => fake()->randomNumber(),
            'name' => fake()->randomNumber(),
            'products' => fake()->randomNumber(),
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.cpf.0', trans('validation.string', [
                    'attribute' => trans('validation.attributes.cpf'),
                ]))
                ->where('errors.name.0', trans('validation.string', [
                    'attribute' => trans('validation.attributes.name'),
                ]))
                ->where('errors.products.0', trans('validation.string', [
                    'attribute' => trans('validation.attributes.products'),
                ]))
        );
    }

    public function test_should_return_an_error_when_the_cpf_and_name_params_are_longer_than_255_characters(): void
    {
        Sanctum::actingAs($this->owner);

        $route = $this->validateDeviceRoute($this->deviceNotValidated->id);

        $response = $this->postJson($route, [
            'cpf' => Str::random(256),
            'name' => Str::random(256),
            'products' => fake()->text(),
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.cpf.0', trans('validation.max.string', [
                    'attribute' => trans('validation.attributes.cpf'),
                    'max' => 255,
                ]))
                ->where('errors.name.0', trans('validation.max.string', [
                    'attribute' => trans('validation.attributes.name'),
                    'max' => 255,
                ]))
        );
    }

    public function test_should_return_an_error_when_the_products_param_are_longer_than_16000_characters(): void
    {
        Sanctum::actingAs($this->owner);

        $route = $this->validateDeviceRoute($this->deviceNotValidated->id);

        $response = $this->postJson($route, [
            'cpf' => $this->owner->cpf,
            'name' => $this->owner->name,
            'products' => Str::random(16001),
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.products.0', trans('validation.max.string', [
                    'attribute' => trans('validation.attributes.products'),
                    'max' => 16000,
                ]))
        );
    }
}
