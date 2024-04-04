<?php

namespace Tests\Feature\Controllers\DeviceTransferController;

use App\Enums\Device\DeviceValidationStatus;
use App\Http\Messages\FlashMessage;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateDeviceTransferTest extends TestCase
{
    use RefreshDatabase;

    private User $sourceUser;
    private User $targetUser;
    private Device $device;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->sourceUser = User::factory()->create();
        $this->targetUser = User::factory()->create();

        $deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->device = Device::factory()
            ->for($this->sourceUser)
            ->for($deviceModel)
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED,
            ]);
    }

    public function test_an_unauthenticated_user_should_not_be_allowed_to_create_device_transfers(): void
    {
        $response = $this->postJson("/api/devices/{$this->device->id}", [
            'target_user_id' => $this->targetUser->id,
        ]);

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_the_owner_of_the_device_must_be_authorized_to_create_transfers(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $response = $this->postJson("/api/devices/{$this->device->id}", [
            'target_user_id' => $this->targetUser->id,
        ]);

        $response->assertCreated()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans_choice('flash_messages.success.created.f', 1, [
                    'model' => trans_choice('model.device_transfer', 1),
                ]))
        );
    }

    public function test_a_user_should_not_be_authorized_to_create_transfers_from_devices_that_do_not_belong_to_them(): void
    {
        Sanctum::actingAs($this->targetUser);

        $response = $this->postJson("/api/devices/{$this->device->id}", [
            'target_user_id' => $this->targetUser->id,
        ]);

        $response->assertForbidden()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthorized'))
        );
    }

    public function test_should_return_an_error_when_the_target_user_id_param_is_null(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $response = $this->postJson("/api/devices/{$this->device->id}", [
            'target_user_id' => null,
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.target_user_id.0', trans('validation.required', [
                    'attribute' => trans('validation.attributes.target_user_id'),
                ]))
        );
    }

    public function test_should_return_an_error_when_the_target_user_id_param_is_not_numeric(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $nonNumericId = Str::random(4);

        $response = $this->postJson("/api/devices/{$this->device->id}", [
            'target_user_id' => $nonNumericId,
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.target_user_id.0', trans('validation.numeric', [
                    'attribute' => trans('validation.attributes.target_user_id'),
                ]))
        );
    }

    public function test_should_return_an_error_when_the_target_user_id_param_does_not_exist_in_the_database(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $lastUser = User::latest('id')->first();
        $nonExistentId = $lastUser->id + 1;

        $response = $this->postJson("/api/devices/{$this->device->id}", [
            'target_user_id' => $nonExistentId,
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.target_user_id.0', trans('validation.exists', [
                    'attribute' => trans('validation.attributes.target_user_id'),
                ]))
        );
    }

    public function test_should_return_an_error_when_the_target_user_id_param_is_a_boolean_value(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $booleanValues = [true, false];

        foreach ($booleanValues as $value) {
            $response = $this->postJson("/api/devices/{$this->device->id}", [
                'target_user_id' => $value,
            ]);

            $response->assertUnprocessable()->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('errors.target_user_id.0', trans('validation.numeric', [
                        'attribute' => trans('validation.attributes.target_user_id'),
                    ]))
                    ->where('errors.target_user_id.1', trans('validation.custom.attribute.not_boolean', [
                        'attribute' => trans('validation.attributes.target_user_id'),
                    ]))
            );
        }
    }
}
