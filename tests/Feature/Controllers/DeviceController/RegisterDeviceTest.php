<?php

namespace Tests\Feature\Controllers\DeviceController;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;
use App\Traits\RandomNumberGenerator;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegisterDeviceTest extends TestCase
{
    use RefreshDatabase;
    use RandomNumberGenerator;

    private User $user;
    private Invoice $invoice;

    private DeviceModel $deviceModel;
    private Device $device;

    protected function setUp(): void
    {
        parent::SetUp();
        $this->seed();

        $this->user = User::factory()->create();

        $this->deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->invoice = Invoice::factory()->make();

        $this->device = Device::factory()
            ->for($this->user)
            ->for($this->deviceModel)
            ->make();
    }

    public function test_an_unauthenticated_user_must_not_be_authorized_to_register_a_device(): void
    {
        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthenticated'))
                    ->etc()
            );
    }

    public function test_an_authenticated_user_must_be_authorized_to_register_a_device(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertCreated();
    }

    public function test_should_return_an_error_when_the_color_param_is_null(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->postJson("/api/devices", [
            'color' => null,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.color.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.color')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_color_param_is_longer_than_255_characters(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->postJson("/api/devices", [
            'color' => Str::random(256),
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.color.0', trans('validation.max.string', [
                        'max' => 255,
                        'attribute' => trans('validation.attributes.color')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_acccess_key_param_is_null(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => null,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.access_key.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.access_key')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_access_key_param_is_not_44_digits_long(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $invalidAccessKeys = [
            Str::random(10), self::generateRandomNumber(43), self::generateRandomNumber(45)
        ];

        foreach ($invalidAccessKeys as $invalidAccessKey) {
            $response = $this->postJson("/api/devices", [
                'color' => $this->device->color,
                'access_key' => $invalidAccessKey,
                'device_model_id' => $this->deviceModel->id,
                'imei_1' => $this->device->imei_1,
                'imei_2' => $this->device->imei_2
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJson(
                    fn (AssertableJson $json) =>
                    $json->has('errors', 1)
                        ->where('errors.access_key.0', trans('validation.digits', [
                            'digits' => 44,
                            'attribute' => trans('validation.attributes.access_key')
                        ]))
                        ->etc()
                );
        }
    }

    public function test_should_return_an_error_when_the_access_key_param_already_exists_in_the_database(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $existingAccessKey = Invoice::firstOrFail()->access_key;

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $existingAccessKey,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.access_key.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.access_key')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_device_model_id_param_is_null(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => null,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.device_model_id.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.device_model_id')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_device_model_id_param_does_not_exist_in_the_database(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $lastDeviceModelRecord = DeviceModel::latest('id')->first();
        $nonExistentId = $lastDeviceModelRecord->id + 1;

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $nonExistentId,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.device_model_id.0', trans('validation.exists', [
                        'attribute' => trans('validation.attributes.device_model_id')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_device_model_id_param_is_not_numeric(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $nonNumericId = Str::random(4);

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $nonNumericId,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.device_model_id.0', trans('validation.numeric', [
                        'attribute' => trans('validation.attributes.device_model_id')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_imei_1_param_is_null(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => null,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.imei_1.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.imei_1')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_imei_1_param_is_not_unique_in_database(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $existingImei = Device::first()->imei_1;

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $existingImei,
            'imei_2' => $this->device->imei_2
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.imei_1.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.imei_1')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_imei_1_param_does_not_have_15_digits(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $invalidImeis = [
            Str::random(10), self::generateRandomNumber(16), self::generateRandomNumber(14)
        ];

        foreach ($invalidImeis as $invalidImei) {
            $response = $this->postJson("/api/devices", [
                'color' => $this->device->color,
                'access_key' => $this->invoice->access_key,
                'device_model_id' => $this->deviceModel->id,
                'imei_1' => $invalidImei,
                'imei_2' => $this->device->imei_2
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJson(
                    fn (AssertableJson $json) =>
                    $json->has('errors', 1)
                        ->where('errors.imei_1.0', trans('validation.digits', [
                            'digits' => 15,
                            'attribute' => trans('validation.attributes.imei_1')
                        ]))
                        ->etc()
                );
        }
    }

    public function test_should_return_an_error_when_the_imei_2_param_is_null(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $this->device->imei_1,
            'imei_2' => null
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.imei_2.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.imei_2')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_imei_2_param_is_not_unique_in_database(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $existingImei = Device::first()->imei_1;

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $existingImei
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.imei_2.0', trans('validation.unique', [
                        'attribute' => trans('validation.attributes.imei_2')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_imei_2_param_does_not_have_15_digits(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $invalidImeis = [
            Str::random(10), self::generateRandomNumber(16), self::generateRandomNumber(14)
        ];

        foreach ($invalidImeis as $invalidImei) {
            $response = $this->postJson("/api/devices", [
                'color' => $this->device->color,
                'access_key' => $this->invoice->access_key,
                'device_model_id' => $this->deviceModel->id,
                'imei_1' => $this->device->imei_1,
                'imei_2' => $invalidImei
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJson(
                    fn (AssertableJson $json) =>
                    $json->has('errors', 1)
                        ->where('errors.imei_2.0', trans('validation.digits', [
                            'digits' => 15,
                            'attribute' => trans('validation.attributes.imei_2')
                        ]))
                        ->etc()
                );
        }
    }

    public function test_should_return_an_error_when_the_params_imei_1_and_imei_2_are_the_same(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->postJson("/api/devices", [
            'color' => $this->device->color,
            'access_key' => $this->invoice->access_key,
            'device_model_id' => $this->deviceModel->id,
            'imei_1' => $this->device->imei_1,
            'imei_2' => $this->device->imei_1
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.imei_1.0', trans('validation.different', [
                        'attribute' => trans('validation.attributes.imei_1'),
                        'other' => trans('validation.attributes.imei_2')
                    ]))
                    ->etc()
            );
    }
}
